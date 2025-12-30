<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SupplierDebt;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CashboxService;

class PurchaseInvoiceController extends Controller
{
    protected $cashboxService;

    public function __construct(CashboxService $cashboxService)
    {
        $this->cashboxService = $cashboxService;
    }

    private function generateInvoiceNumber()
    {
        do {
            $number = 'B-' . rand(100000, 999999);
        } while (PurchaseInvoice::where('invoice_number', $number)->exists());
        return $number;
    }

    public function index(Request $request)
    {
        $invoices = PurchaseInvoice::with('supplier')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('invoice_number', 'like', "%{$request->search}%")
                        ->orWhereHas('supplier', function($supplierQuery) use ($request) {
                            $supplierQuery->where('name', 'like', "%{$request->search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.purchase_invoices.index', compact('invoices'))->render();
        }

        return view('admin.purchase_invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = PurchaseInvoice::with('supplier', 'items.product')->findOrFail($id);
        return view('admin.purchase_invoices.show', compact('invoice'));
    }

    public function create()
    {
        return view('admin.purchase_invoices.create', [
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
            'cashboxes' => Cashbox::all(),
            'invoice_number' => $this->generateInvoiceNumber(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'payment_status' => 'required|in:paid,partial,due',
            'additional_expenses' => 'nullable|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.cashbox_id' => 'required_with:payments|exists:cashboxes,id',
            'payments.*.amount' => 'required_with:payments|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $invoice = PurchaseInvoice::create([
                'invoice_number' => $data['invoice_number'],
                'supplier_id' => $data['supplier_id'],
                'date' => $data['date'],
                'payment_status' => $data['payment_status'],
                'note' => $request->note,
                'additional_expenses' => $data['additional_expenses'] ?? 0,
                'total_cost' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $row) {
                $rowTotal = $row['quantity'] * $row['purchase_price'];
                $total += $rowTotal;

                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'purchase_price' => $row['purchase_price'],
                    'total' => $rowTotal,
                ]);

                $product = Product::find($row['product_id']);
                $product->increment('stock', $row['quantity']);
                $product->recalcPurchaseData();
            }

            $total += $invoice->additional_expenses;

            $paid = 0;
            $due = $total;

            if (isset($data['payments']) && !empty($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    $amount = $payment['amount'];
                    $paid += $amount;
                    $this->cashboxService->addTransaction(
                        $payment['cashbox_id'], 'out', $amount,
                        'purchase_invoice', $invoice->id,
                        'سداد فاتورة شراء #' . $invoice->invoice_number,
                        $data['date']
                    );
                }
                $due = $total - $paid;
            }

            if ($data['payment_status'] == 'paid' && $paid != $total) {
                throw new \Exception('يجب دفع المبلغ الكامل للفاتورة');
            }
            if ($data['payment_status'] == 'partial' && $paid >= $total) {
                throw new \Exception('لا يمكن دفع المبلغ الكامل كدفع جزئي');
            }
            if ($data['payment_status'] == 'due' && $paid > 0) {
                throw new \Exception('لا يمكن الدفع في حالة الأجل');
            }

            $invoice->update([
                'total_cost' => $total,
                'paid_amount' => $paid,
                'due_amount' => $due,
            ]);

            if($due > 0){
                SupplierDebt::create([
                    'supplier_id'=>$data['supplier_id'],
                    'purchase_invoice_id'=>$invoice->id,
                    'amount'=>$due,
                    'type'=>'debit',
                    'notes'=>'مديونية فاتورة شراء #'.$invoice->invoice_number,
                    'date'=>$data['date'],
                ]);
            }

            $supplier = Supplier::find($data['supplier_id']);
            if (!$supplier->last_supply_date || $supplier->last_supply_date < $data['date']) {
                $supplier->update(['last_supply_date' => $data['date']]);
            }

            DB::commit();
            return redirect()->route('admin.purchase_invoices.index')->with('success','تم إنشاء فاتورة الشراء بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $invoice = PurchaseInvoice::with('items.product', 'payments')->findOrFail($id);

        return view('admin.purchase_invoices.edit', [
            'invoice' => $invoice,
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
            'cashboxes' => Cashbox::all(),
            'payments' => $invoice->payments
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = PurchaseInvoice::with('items', 'payments')->findOrFail($id);

        $data = $request->validate([
            'supplier_id'=>'required|exists:suppliers,id',
            'date'=>'required|date',
            'payment_status'=>'required|in:paid,partial,due',
            'additional_expenses'=>'nullable|numeric|min:0',
            'payments'=>'nullable|array',
            'payments.*.cashbox_id'=>'required_with:payments|exists:cashboxes,id',
            'payments.*.amount'=>'required_with:payments|numeric|min:0.01',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id',
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.purchase_price'=>'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach($invoice->items as $item){
                $item->product->decrement('stock',$item->quantity);
            }

            CashboxTransaction::where('module','purchase_invoice')->where('module_id',$invoice->id)->delete();

            SupplierDebt::where('purchase_invoice_id',$invoice->id)->delete();

            $invoice->items()->delete();

            $invoice->update([
                'supplier_id'=>$data['supplier_id'],
                'date'=>$data['date'],
                'payment_status'=>$data['payment_status'],
                'additional_expenses'=>$data['additional_expenses'] ?? 0,
                'note'=>$request->note,
            ]);

            $total = 0;
            foreach($data['items'] as $row){
                $lineTotal = $row['quantity'] * $row['purchase_price'];
                $total += $lineTotal;
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id'=>$invoice->id,
                    'product_id'=>$row['product_id'],
                    'quantity'=>$row['quantity'],
                    'purchase_price'=>$row['purchase_price'],
                    'total'=>$lineTotal,
                ]);
                $product = Product::find($row['product_id']);
                $product->increment('stock',$row['quantity']);
                $product->recalcPurchaseData();
            }

            $total += $invoice->additional_expenses;

            $paid = 0;
            if(isset($data['payments']) && !empty($data['payments'])){
                foreach($data['payments'] as $payment){
                    $amount = $payment['amount'];
                    $paid += $amount;
                    $this->cashboxService->addTransaction(
                        $payment['cashbox_id'],
                        'out',
                        $amount,
                        'purchase_invoice',
                        $invoice->id,
                        'تعديل فاتورة شراء #'.$invoice->invoice_number,
                        $data['date']
                    );
                }
            }

            $due = $total - $paid;

            // التحقق من الدفع
            if ($data['payment_status'] == 'paid' && $paid != $total) {
                throw new \Exception('يجب دفع المبلغ الكامل للفاتورة');
            }
            if ($data['payment_status'] == 'partial' && $paid >= $total) {
                throw new \Exception('لا يمكن دفع المبلغ الكامل كدفع جزئي');
            }
            if ($data['payment_status'] == 'due' && $paid > 0) {
                throw new \Exception('لا يمكن الدفع في حالة الأجل');
            }

            if($due>0){
                SupplierDebt::where('purchase_invoice_id',$invoice->id)->delete();
                SupplierDebt::create([
                    'supplier_id'=>$data['supplier_id'],
                    'purchase_invoice_id'=>$invoice->id,
                    'amount'=>$due,
                    'type'=>'debit',
                    'notes'=>'مديونية فاتورة شراء #'.$invoice->invoice_number,
                    'date'=>$data['date'],
                ]);
            }

            $invoice->update(['total_cost'=>$total,'paid_amount'=>$paid,'due_amount'=>$due]);

            $supplier = Supplier::find($data['supplier_id']);
            if (!$supplier->last_supply_date || $supplier->last_supply_date < $data['date']) {
                $supplier->update(['last_supply_date' => $data['date']]);
            }

            DB::commit();
            return redirect()->route('admin.purchase_invoices.index')->with('success','تم تعديل الفاتورة بنجاح');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $invoice = PurchaseInvoice::with('items', 'payments')->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach($invoice->items as $item){
                $item->product->decrement('stock',$item->quantity);
            }

            foreach($invoice->payments as $p){
                $this->cashboxService->addTransaction(
                    $p->cashbox_id,
                    'in',
                    $p->amount,
                    'purchase_invoice',
                    $invoice->id,
                    'حذف فاتورة شراء #'.$invoice->invoice_number,
                    $invoice->date
                );
            }

            SupplierDebt::where('purchase_invoice_id',$invoice->id)->delete();
            $invoice->items()->delete();
            $invoice->delete();

            $lastInvoice = PurchaseInvoice::where('supplier_id', $invoice->supplier_id)
                                ->orderBy('date','desc')
                                ->first();
            $supplier = Supplier::find($invoice->supplier_id);
            $supplier->update([
                'last_supply_date' => $lastInvoice ? $lastInvoice->date : null
            ]);

            DB::commit();
            return redirect()->route('admin.purchase_invoices.index')->with('success','تم حذف الفاتورة بنجاح');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()]);
        }
    }
}
