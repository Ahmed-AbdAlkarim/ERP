<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.customers.partials.table', compact('customers'))->render();
        }

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'last_purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

       
        if (auth()->user()->can('show_customer_debts')) {
            $rules['debt'] = 'nullable|numeric';
        }

        $data = $request->validate($rules);

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'last_purchase_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

        if (auth()->user()->can('show_customer_debts')) {
            $rules['debt'] = 'nullable|numeric';
        }

        $data = $request->validate($rules);

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'تم حذف العميل');
    }
}
