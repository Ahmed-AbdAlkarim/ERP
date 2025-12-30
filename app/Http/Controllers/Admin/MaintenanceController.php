<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Customer;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Services\CashboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with(['customer', 'createdBy', 'updatedBy'])
            ->latest()
            ->paginate(15);

        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('admin.maintenances.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'device_type'   => 'required|string|max:255',
            'fault_type'    => 'required|string|max:255',
            'cost'          => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'status'        => 'required|in:pending,in_progress,completed,delivered',
            'notes'         => 'nullable|string',
        ]);

        Maintenance::create([
            'customer_id'   => $request->customer_id,
            'device_type'   => $request->device_type,
            'fault_type'    => $request->fault_type,
            'cost'          => $request->cost,
            'delivery_date' => $request->delivery_date,
            'status'        => $request->status,
            'notes'         => $request->notes,
            'created_by'    => auth()->id(),
        ]);

        return redirect()
            ->route('admin.maintenances.index')
            ->with('success', 'تم إضافة الصيانة بنجاح');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['customer', 'createdBy', 'updatedBy']);

        return view('admin.maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $customers = Customer::all();

        return view('admin.maintenances.edit', compact('maintenance', 'customers'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'device_type'   => 'required|string|max:255',
            'fault_type'    => 'required|string|max:255',
            'cost'          => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date',
            'status'        => 'required|in:pending,in_progress,completed,delivered',
            'notes'         => 'nullable|string',
        ]);

        $oldCost = $maintenance->cost;

        $maintenance->update([
            'customer_id'   => $request->customer_id,
            'device_type'   => $request->device_type,
            'fault_type'    => $request->fault_type,
            'cost'          => $request->cost,
            'delivery_date' => $request->delivery_date,
            'status'        => $request->status,
            'notes'         => $request->notes,
            'updated_by'    => auth()->id(),
        ]);

        // لو المبلغ اتغير وكان متحصّل قبل كده
        if ($oldCost != $maintenance->cost) {
            $transaction = CashboxTransaction::where('module', 'maintenance_collection')
                ->where('module_id', $maintenance->id)
                ->first();

            if ($transaction) {
                $difference = $maintenance->cost - $oldCost;

                $transaction->update([
                    'amount' => $maintenance->cost,
                ]);

                if ($difference > 0) {
                    $transaction->cashbox->increment('balance', $difference);
                } elseif ($difference < 0) {
                    $transaction->cashbox->decrement('balance', abs($difference));
                }
            }
        }

        return redirect()
            ->route('admin.maintenances.index')
            ->with('success', 'تم تحديث الصيانة بنجاح');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()
            ->route('admin.maintenances.index')
            ->with('success', 'تم حذف الصيانة بنجاح');
    }

    public function collect(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'cashbox_id' => 'required|exists:cashboxes,id',
        ]);

        $cashbox = Cashbox::findOrFail($request->cashbox_id);

        try {
            DB::transaction(function () use ($maintenance, $cashbox) {
                $cashboxService = new CashboxService();

                $cashboxService->addTransaction(
                    $cashbox->id,
                    'in',
                    $maintenance->cost,
                    'maintenance_collection',
                    $maintenance->id,
                    "تحصيل صيانة للعميل {$maintenance->customer->name}"
                );
            });

            return redirect()
                ->route('admin.maintenances.index')
                ->with('success', 'تم تحصيل المبلغ بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'فشل في التحصيل: ' . $e->getMessage());
        }
    }
}
