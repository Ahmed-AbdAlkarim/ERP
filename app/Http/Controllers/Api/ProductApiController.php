<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->where('stock', '>', 0) // ميعرضش لو المخزون صفر
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type); // فلترة حسب الفئة
            })
            ->select([
                'id',
                'name',
                'selling_price',
                'image',
                'type',
                'stock'
            ])
            ->get()
            ->map(function ($product) {
                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->selling_price,
                    'type'  => $product->type,
                    'image' => $product->image 
                        ? asset('storage/' . $product->image)
                        : null,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $products
        ]);
    }
}
