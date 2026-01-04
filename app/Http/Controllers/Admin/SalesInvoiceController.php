<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CashboxService;
use Mpdf\Mpdf;

class SalesInvoiceController extends Controller
{
    protected $cashboxService;

    public function __construct(CashboxService $cashboxService)
    {
        $this->cashboxService = $cashboxService;
    }

    public function index(Request $request)
    {
        $invoices = SalesInvoice::with('customer')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('invoice_number', 'like', "%{$request->search}%")
                        ->orWhereHas('customer', function($customerQuery) use ($request) {
                            $customerQuery->where('name', 'like', "%{$request->search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.sales_invoices.index', compact('invoices'))->render();
        }

        return view('admin.sales_invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with('items.product', 'customer')
            ->findOrFail($id);

        if (!auth()->user()->can('show_sales_invoice_profit')) {
            $invoice->profit = null;
        }

        return view('admin.sales_invoices.show', compact('invoice'));
    }


    public function create()
    {
        return view('admin.sales_invoices.create', [
            'customers'=>Customer::all(),
            'products'=>Product::all(),
            'cashboxes'=> \App\Models\Cashbox::where('type', 'daily')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_date' => 'required|date',
            'customer_id'  => 'nullable|exists:customers,id',
            'payment_status' => 'required|in:paid,partial,due',

            'payments' => 'nullable|required_if:payment_status,paid,partial|array|min:1',
            'payments.*.method' => 'required|in:cashbox,customer_balance',
            'payments.*.cashbox_id' => 'nullable|required_if:payments.*.method,cashbox|exists:cashboxes,id',
            'payments.*.amount' => 'required|numeric|min:0.01',

            'discount' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            /* ================= الحسابات ================= */
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }

            $discount = $data['discount'] ?? 0;
            $total    = $subtotal - $discount;

            $paid = 0;
            if (in_array($data['payment_status'], ['paid', 'partial'])) {
                foreach ($data['payments'] as $p) {
                    $paid += $p['amount'];
                }
            }

            $remaining = $total - $paid;

            /* ================= التحقق ================= */
            if ($data['payment_status'] === 'paid' && $remaining != 0) {
                throw new \Exception('يجب سداد كامل المبلغ في حالة مدفوع');
            }

            if ($data['payment_status'] === 'partial' && ($paid <= 0 || $paid >= $total)) {
                throw new \Exception('قيمة الدفع الجزئي غير صحيحة');
            }

            if ($data['payment_status'] === 'due' && $paid > 0) {
                throw new \Exception('لا يمكن الدفع في حالة الأجل');
            }

            /* ================= إنشاء الفاتورة ================= */
            $paymentMethod = match ($data['payment_status']) {
                'paid', 'partial' => 'cash',
                'due' => 'installment',
            };

            $invoice = SalesInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date'   => $data['invoice_date'],
                'customer_id'    => $data['customer_id'],
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'status'         => $data['payment_status'],
                'paid_amount'    => $paid,
                'remaining_amount' => $remaining,
            ]);

            /* ================= الأصناف والمخزون ================= */
            $totalCost = 0;

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                $lineCost  = $product->avg_cost * $item['qty'];
                $totalCost += $lineCost;

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id'       => $product->id,
                    'qty'              => $item['qty'],
                    'price'            => $item['price'],
                    'total'            => $item['qty'] * $item['price'],
                    'profit'           => 0,
                ]);

                if (!$product->is_service) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            $invoice->update(['profit' => $total - $totalCost]);

            /* ================= تنفيذ الدفعات ================= */
            if ($paid > 0 && in_array($data['payment_status'], ['paid', 'partial'])) {

                foreach ($data['payments'] as $payment) {

                    // 🔹 الدفع من الخزنة
                    if ($payment['method'] === 'cashbox') {

                        $this->cashboxService->addTransaction(
                            $payment['cashbox_id'],
                            'in',
                            $payment['amount'],
                            'sales_invoice',
                            $invoice->id,
                            'تحصيل فاتورة بيع #' . $invoice->invoice_number
                        );
                    }

                    // 🔹 الدفع من رصيد العميل
                    if ($payment['method'] === 'customer_balance') {

                        if (!$invoice->customer_id) {
                            throw new \Exception('لا يمكن الدفع من رصيد بدون تحديد عميل');
                        }

                        $customer = Customer::lockForUpdate()->find($invoice->customer_id);

                        if ($customer->balance < $payment['amount']) {
                            throw new \Exception('رصيد العميل غير كافٍ');
                        }

                        $customer->decrement('balance', $payment['amount']);
                    }
                }
            }

            /* ================= مديونية العميل ================= */
            if ($invoice->customer_id) {
                $customer = Customer::find($invoice->customer_id);

                if ($remaining > 0) {
                    $customer->increment('debt', $remaining);
                }

                $customer->last_purchase_date = $data['invoice_date'];
                $customer->save();
            }

            DB::commit();
            return redirect()
                ->route('admin.sales-invoices.index')
                ->with('success', 'تم إنشاء فاتورة البيع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }




    public function edit($id)
    {
        $invoice = SalesInvoice::with('items.product')->findOrFail($id);
        $existingPayments = [];
        if ($invoice->paid_amount > 0) {
            $transactions = \App\Models\CashboxTransaction::where('module', 'sales_invoice')
                ->where('module_id', $id)
                ->where('type', 'in')
                ->get();
            foreach ($transactions as $transaction) {
                $existingPayments[] = [
                    'cashbox_id' => $transaction->cashbox_id,
                    'amount' => $transaction->amount,
                ];
            }
        }
        return view('admin.sales_invoices.edit', [
            'invoice'=>$invoice,
            'customers'=>Customer::all(),
            'products'=>Product::all(),
            'cashboxes'=> \App\Models\Cashbox::where('type', 'daily')->get(),
            'existingPayments' => $existingPayments,
        ]);
    }

    public function update(Request $request,$id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        $data = $request->validate([
            'invoice_date'=>'required|date',
            'customer_id'=>'nullable|exists:customers,id',
            'payment_status'=>'required|in:paid,partial,due',
            'payments'=>'nullable|required_if:payment_status,paid,partial|array|min:1',
            'payments.*.cashbox_id'=>'required|exists:cashboxes,id',
            'payments.*.amount'=>'required|numeric|min:0.01',
            'discount'=>'nullable|numeric|min:0',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id',
            'items.*.qty'=>'required|integer|min:1',
            'items.*.price'=>'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach($invoice->items as $item){
                if(!$item->product->is_service){
                    $item->product->increment('stock',$item->qty);
                }
            }

            \App\Models\CashboxTransaction::where('module','sales_invoice')->where('module_id',$invoice->id)->delete();

            if($invoice->customer_id && $invoice->remaining_amount>0){
                $customer = Customer::find($invoice->customer_id);
                $customer->decrement('debt',$invoice->remaining_amount);
            }

            $invoice->items()->delete();

            $invoice->update([
                'invoice_date'=>$data['invoice_date'],
                'customer_id'=>$data['customer_id'],
            ]);

            $subtotal = 0;
            $totalCost = 0;
            foreach($data['items'] as $item){
                $product = Product::lockForUpdate()->find($item['product_id']);
                $lineCost = $product->avg_cost * $item['qty'];
                $totalCost += $lineCost;
                $subtotal += $item['qty']*$item['price'];

                SalesInvoiceItem::create([
                    'sales_invoice_id'=>$invoice->id,
                    'product_id'=>$product->id,
                    'qty'=>$item['qty'],
                    'price'=>$item['price'],
                    'total'=>$item['qty']*$item['price'],
                    'profit'=>0,
                ]);

                if(!$product->is_service){
                    $product->decrement('stock',$item['qty']);
                }
            }

            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            $paid = 0;
            if(isset($data['payments']) && !empty($data['payments'])){
                foreach($data['payments'] as $payment){
                    $amount = $payment['amount'];
                    $paid += $amount;
                    $this->cashboxService->addTransaction(
                        $payment['cashbox_id'],
                        'in',
                        $amount,
                        'sales_invoice',
                        $invoice->id,
                        'تعديل فاتورة بيع #'.$invoice->invoice_number,
                        $data['invoice_date']
                    );
                }
            }

            $remaining = $total - $paid;
            $status = $data['payment_status'];

            if ($data['payment_status'] == 'paid' && $paid != $total) {
                throw new \Exception('يجب دفع المبلغ الكامل للفاتورة');
            }
            if ($data['payment_status'] == 'partial' && $paid >= $total) {
                throw new \Exception('لا يمكن دفع المبلغ الكامل كدفع جزئي');
            }
            if ($data['payment_status'] == 'due' && $paid > 0) {
                throw new \Exception('لا يمكن الدفع في حالة الأجل');
            }

            $paymentMethod = match($data['payment_status']) {
                'paid', 'partial' => 'cash',
                'due' => 'installment',
            };

            $invoice->update([
                'payment_method'=>$paymentMethod,
                'subtotal'=>$subtotal,
                'discount'=>$discount,
                'total'=>$total,
                'paid_amount'=>$paid,
                'remaining_amount'=>$remaining,
                'status'=>$status,
                'profit'=>$total-$totalCost,
            ]);

            if($remaining>0 && $invoice->customer_id){
                $customer = Customer::find($invoice->customer_id);
                $customer->increment('debt',$remaining);
                $customer->last_purchase_date = $data['invoice_date'];
                $customer->save();
            }

            DB::commit();
            return redirect()->route('admin.sales-invoices.index')->with('success','تم تعديل فاتورة البيع بنجاح');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        DB::beginTransaction();
        try {
            foreach($invoice->items as $item){
                if(!$item->product->is_service){
                    $item->product->increment('stock',$item->qty);
                }
            }
            if($invoice->paid_amount>0){
                $transactions = \App\Models\CashboxTransaction::where('module', 'sales_invoice')
                    ->where('module_id', $invoice->id)
                    ->where('type', 'in')
                    ->get();
                foreach($transactions as $transaction){
                    $this->cashboxService->revertTransaction($transaction->cashbox_id, $transaction->amount, 'sales_invoice', $invoice->id);
                }
            }
            if($invoice->customer_id && $invoice->remaining_amount>0){
                $customer = Customer::find($invoice->customer_id);
                $customer->decrement('debt',$invoice->remaining_amount);
            }

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();
            return redirect()->route('admin.sales-invoices.index')->with('success','تم حذف الفاتورة بنجاح');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()]);
        }
    }


    public function print($id)
    {
        $invoice = SalesInvoice::with('items.product', 'customer')->findOrFail($id);

        $data = [
            'invoice' => $invoice,
            'items'   => $invoice->items,
            'customer'=> $invoice->customer,
        ];

        $html = view('admin.sales_invoices.pdf', $data)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);
        $fileName = 'invoice-'.$invoice->invoice_number.'.pdf';
        return $mpdf->Output($fileName, 'I');
    }

    private function generateInvoiceNumber()
    {
        return 'S-' . date('Ymd') . '-' . rand(1000,9999);
    }
}
