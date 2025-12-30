<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Services\CashboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('cashbox');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('cashbox_id')) {
            $query->where('cashbox_id', $request->cashbox_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

      
        $totalAmount = (clone $query)->sum('amount');

        $expenses = $query
            ->orderByDesc('expense_date')
            ->paginate(20);

        $safes = Cashbox::all();

        return view('admin.expenses.index', compact(
            'expenses',
            'safes',
            'totalAmount'
        ));
    }

    public function create()
    {
        $safes = Cashbox::all();
        return view('admin.expenses.create', compact('safes'));
    }

    public function store(Request $request, CashboxService $cashboxService)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in([
                'electricity', 'rent', 'salaries',
                'shipping', 'maintenance', 'marketing', 'office'
            ])],
            'amount' => 'required|numeric|min:0.01',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'expense_date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense = Expense::create($data);

        $cashboxService->addTransaction(
            $data['cashbox_id'],
            'out',
            $data['amount'],
            'expense',
            $expense->id,
            'مصروف: ' . $data['title'],
            $data['expense_date']
        );

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'تم إضافة المصروف بنجاح');
    }

    public function edit(Expense $expense)
    {
        $safes = Cashbox::all();
        return view('admin.expenses.edit', compact('expense', 'safes'));
    }

    public function update(Request $request, Expense $expense, CashboxService $cashboxService)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in([
                'electricity', 'rent', 'salaries',
                'shipping', 'maintenance', 'marketing', 'office'
            ])],
            'amount' => 'required|numeric|min:0.01',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'expense_date' => 'required|date',
            'attachment' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

     
        $oldTransaction = CashboxTransaction::where('module', 'expense')
            ->where('module_id', $expense->id)
            ->first();

        if ($oldTransaction) {
            $cashboxService->revertTransaction(
                $oldTransaction->cashbox_id,
                $oldTransaction->amount,
                'expense',
                $expense->id
            );
            $oldTransaction->delete();
        }

        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        $cashboxService->addTransaction(
            $data['cashbox_id'],
            'out',
            $data['amount'],
            'expense',
            $expense->id,
            'تعديل مصروف: ' . $data['title'],
            $data['expense_date']
        );

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'تم تعديل المصروف بنجاح');
    }

    public function destroy(Expense $expense, CashboxService $cashboxService)
    {
 
        $transaction = CashboxTransaction::where('module', 'expense')
            ->where('module_id', $expense->id)
            ->latest()
            ->first();

        if ($transaction && $transaction->type === 'out') {
            $cashboxService->revertTransaction(
                $transaction->cashbox_id,
                $transaction->amount,
                $transaction->module,
                $transaction->module_id
            );

            $transaction->delete();
        }

 
        CashboxTransaction::where('module', 'expense')
            ->where('module_id', $expense->id)
            ->delete();

        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->back()->with('success', 'تم حذف المصروف ورجوع المبلغ للخزنة');
    }

}
