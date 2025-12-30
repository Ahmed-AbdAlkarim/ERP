<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $products = Product::when($r->search, function ($q) use ($r) {
            $q->where(function($query) use ($r) {
                $query->where('name', 'like', "%{$r->search}%")
                    ->orWhere('sku', 'like', "%{$r->search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        if ($r->ajax()) {
            return view('admin.products.index', compact('products'))->render();
        }

        return view('admin.products.index', compact('products'));
    }



    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'=>'required|string|max:255',
            'type'=>['required',Rule::in(['laptop','mobile','accessory','spare','service'])],
            'model'=>'nullable|string|max:255',
            'sku'=>'nullable|string|unique:products,sku', 
            'purchase_price'=>'required|numeric|min:0',
            'selling_price'=>'required|numeric|min:0',
            'min_allowed_price'=>'nullable|numeric|min:0',
            'warranty_type'=>'nullable|string|max:255',
            'warranty_period_days'=>'nullable|integer|min:0',
            'condition'=>['required',Rule::in(['new','used','imported'])],
            'image'=>'nullable|image|max:2048',
            'stock'=>'required|integer|min:0',
            'reorder_level'=>'nullable|integer|min:0',
            'is_service'=>'boolean',
            'notes'=>'nullable|string',
        ]);

        if(empty($data['sku'])){
            $data['sku'] = 'SKU-' . Str::upper(Str::random(8));
        }

        if($r->hasFile('image')){
            $data['image'] = $r->file('image')->store('products','public');
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success','تم إضافة الصنف');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $r, Product $product)
    {
        $data = $r->validate([
            'name'=>'required|string|max:255',
            'type'=>['required',Rule::in(['laptop','mobile','accessory','spare','service'])],
            'model'=>'nullable|string|max:255',
            'sku'=>['nullable','string', Rule::unique('products','sku')->ignore($product->id)],
            'purchase_price'=>'required|numeric|min:0',
            'selling_price'=>'required|numeric|min:0',
            'min_allowed_price'=>'nullable|numeric|min:0',
            'warranty_type'=>'nullable|string|max:255',
            'warranty_period_days'=>'nullable|integer|min:0',
            'condition'=>['required',Rule::in(['new','used','imported'])],
            'image'=>'nullable|image|max:2048',
            'stock'=>'required|integer|min:0',
            'reorder_level'=>'nullable|integer|min:0',
            'is_service'=>'boolean',
            'notes'=>'nullable|string',
        ]);

        if(empty($data['sku'])){
            $data['sku'] = 'SKU-' . Str::upper(Str::random(8));
        }

        if($r->hasFile('image')){
            $data['image'] = $r->file('image')->store('products','public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success','تم تعديل الصنف بنجاح');
    }



    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success','تم حذف الصنف');
    }
}