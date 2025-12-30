<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::when($request->search, function ($q) use ($request) {
            $q->where(function($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        if ($request->ajax()) {
            return view('admin.suppliers.index', compact('suppliers'))->render();
        }

        return view('admin.suppliers.index', compact('suppliers'));
    }


    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
        ]);

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')->with('success', 'تم إضافة المورد');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
        ]);

        $supplier->update($data);
        return redirect()->route('admin.suppliers.index')->with('success', 'تم تحديث المورد');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'تم حذف المورد');
    }

    public function show($id)
    {
        $supplier = Supplier::with('debts.purchaseInvoice')->findOrFail($id);
        return view('admin.suppliers.show', compact('supplier'));
    }
}
