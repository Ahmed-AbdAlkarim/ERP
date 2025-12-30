<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashboxController extends Controller
{
    public function index()
    {
        $mainCashboxes = Cashbox::where('type', 'main')->get();
        $dailyCashboxes = Cashbox::where('type', 'daily')->get();
        return view('admin.cashboxes.index', compact('mainCashboxes', 'dailyCashboxes'));
    }

    public function create()
    {
        return view('admin.cashboxes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255|unique:cashboxes,name',
            'type'=>'required|in:main,daily,other',
            'balance'=>'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use($data) {
            $cashbox = Cashbox::create($data);

            if(!empty($data['balance']) && $data['balance'] > 0){
                CashboxTransaction::create([
                    'cashbox_id'=>$cashbox->id,
                    'type'=>'in',
                    'amount'=>$data['balance'],
                    'module'=>'initial_balance',
                    'module_id'=>null,
                    'note'=>'رصيد ابتدائي عند إنشاء الخزنة',
                    'date'=>now(),
                ]);
            }
        });

        return redirect()->route('admin.cashboxes.index')->with('success','تم إنشاء الخزنة بنجاح');
    }

    public function edit(Cashbox $cashbox)
    {
        return view('admin.cashboxes.edit', compact('cashbox'));
    }

    public function update(Request $request, Cashbox $cashbox)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255|unique:cashboxes,name,'.$cashbox->id,
            'type'=>'required|in:main,daily,other',
            'is_active'=>'sometimes|boolean',
        ]);

        $cashbox->update($data);

        return redirect()->route('admin.cashboxes.index')->with('success','تم تحديث بيانات الخزنة بنجاح');
    }

    public function destroy(Cashbox $cashbox)
    {
        $cashbox->delete();
        return redirect()->route('admin.cashboxes.index')->with('success','تم حذف الخزنة بنجاح');
    }

    public function showTransferForm()
    {
        $cashboxes = Cashbox::where('is_active', true)->get();
        return view('admin.cashboxes.transfer', compact('cashboxes'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_cashbox' => 'required|exists:cashboxes,id',
            'to_cashbox' => 'required|exists:cashboxes,id|different:from_cashbox',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $from = Cashbox::findOrFail($request->from_cashbox);
        $to   = Cashbox::findOrFail($request->to_cashbox);
        $amount = $request->amount;

        if($from->balance < $amount){
            return back()->with('error','الرصيد في الخزنة المصدر أقل من المبلغ المطلوب');
        }

        DB::transaction(function() use ($from, $to, $amount, $request) {
            $from->decrement('balance', $amount);
            $to->increment('balance', $amount);

            // تسجيل المعاملة للخزنة المصدر
            $from->transactions()->create([
                'type' => 'out',
                'amount' => $amount,
                'module' => 'transfer',
                'module_id' => null,
                'note' => $request->note,
                'date' => now(),
                'user_id' => auth()->id(),
            ]);

            $to->transactions()->create([
                'type' => 'in',
                'amount' => $amount,
                'module' => 'transfer',
                'module_id' => null,
                'note' => $request->note,
                'date' => now(),
            ]);
        });

        return redirect()->route('admin.cashboxes.index')->with('success','تم تحويل المبلغ بنجاح');
    }

    public function transactions()
    {
        $transactions = CashboxTransaction::with('cashbox')
            ->orderBy('date', 'desc')
            ->paginate(20); 

        return view('admin.cashboxes.transactions.index', compact('transactions'));
    }


   
    public function show(Cashbox $cashbox)
    {
        $transactions = CashboxTransaction::where('cashbox_id',$cashbox->id)
            ->orderBy('date','desc')->paginate(20);

        return view('admin.cashboxes.show', compact('cashbox','transactions'));
    }
}
