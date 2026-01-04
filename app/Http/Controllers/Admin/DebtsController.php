<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\SupplierDebt;
use App\Models\SalesInvoice;
use App\Models\Cashbox;
use App\Services\CashboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtsController extends Controller
{
    public function index()
    {
        $supplierDebts = SupplierDebt::with(['supplier', 'purchaseInvoice'])
            ->whereHas('supplier', function($q) {
                $q->where('debt', '>', 0);
            })
            ->get()
            ->groupBy('supplier_id');

        $customerDebts = SalesInvoice::with('customer')
            ->where('remaining_amount', '>', 0)
            ->get()
            ->groupBy('customer_id');

        $cashboxes = Cashbox::where('is_active', true)->get();
        $dailyCashboxes = Cashbox::where('type', 'daily')->where('is_active', true)->get();

        return view('admin.debts.index', compact('supplierDebts', 'customerDebts', 'cashboxes', 'dailyCashboxes'));
    }

    public function paySupplierDebt(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        $cashbox = Cashbox::findOrFail($request->cashbox_id);

        if ($supplier->debt < $request->amount) {
            return back()->withErrors(['amount' => 'المبلغ المطلوب أكبر من المديونية الحالية']);
        }

        if ($cashbox->balance < $request->amount) {
            return back()->withErrors(['cashbox_id' => 'رصيد الخزنة غير كافي']);
        }

        DB::transaction(function () use ($request, $supplier) {
        
            $cashboxService = new CashboxService();
            $cashboxService->addTransaction(
                $request->cashbox_id,
                'out',
                $request->amount,
                'supplier_debt_payment',
                $supplier->id,
                'دفعة للمورد ' . $supplier->name
            );

         
            SupplierDebt::create([
                'supplier_id' => $supplier->id,
                'amount' => $request->amount,
                'type' => 'credit',
                'notes' => 'دفعة من الخزنة',
                'date' => now(),
            ]);
        });

        return back()->with('success', 'تم دفع المديونية بنجاح');
    }

    public function receiveCustomerPayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cashbox_id'  => 'required|exists:cashboxes,id',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $cashbox  = Cashbox::findOrFail($request->cashbox_id);

        // لازم خزنة يومية
        if ($cashbox->type !== 'daily') {
            return back()->withErrors(['cashbox_id' => 'يجب اختيار خزنة يومية فقط']);
        }

        // إجمالي مديونية العميل
        $totalDebt = SalesInvoice::where('customer_id', $customer->id)
            ->where('remaining_amount', '>', 0)
            ->sum('remaining_amount');

        DB::transaction(function () use ($request, $customer, $cashbox, $totalDebt) {

            $cashboxService = new CashboxService();

            // 1️⃣ تسجيل دخول المبلغ كله في الخزنة
            $cashboxService->addTransaction(
                $request->cashbox_id,
                'in',
                $request->amount,
                'customer_payment',
                $customer->id,
                'دفعة من العميل ' . $customer->name
            );

            $remainingAmount = $request->amount;

            // 2️⃣ سداد الفواتير بالترتيب
            if ($totalDebt > 0) {

                $invoices = SalesInvoice::where('customer_id', $customer->id)
                    ->where('remaining_amount', '>', 0)
                    ->orderBy('invoice_date')
                    ->lockForUpdate()
                    ->get();

                foreach ($invoices as $invoice) {
                    if ($remainingAmount <= 0) break;

                    $payAmount = min($remainingAmount, $invoice->remaining_amount);

                    $invoice->increment('paid_amount', $payAmount);
                    $invoice->decrement('remaining_amount', $payAmount);

                    $remainingAmount -= $payAmount;
                }

                // خصم اللي اتسدد فعليًا من مديونية العميل
                $customer->decrement('debt', min($request->amount, $totalDebt));
            }

            // 3️⃣ لو في فلوس زيادة → تتحط رصيد للعميل
            if ($remainingAmount > 0) {
                $customer->increment('balance', $remainingAmount);
            }
        });

        return back()->with('success', 'تم استلام الدفعة بنجاح');
    }

}
