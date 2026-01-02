<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Cashbox;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\CashboxService;

class SalesReturnController extends Controller
{
    public function index()
    {
        $returns = SalesReturn::with('customer')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.sales_returns.index', compact('returns'));
    }

    public function show($id)
    {
        $return = SalesReturn::with('items.product', 'customer', 'cashbox')->findOrFail($id);

        return view('admin.sales_returns.show', compact('return'));
    }

    public function create()
    {
        return view('admin.sales_returns.create', [
            'customers' => Customer::all(),
            'products' => Product::all(),
            'cashboxes' => Cashbox::where('type','daily')->get(),
            'return_number' => 'R-' . now()->format('Ymd') . '-' . rand(1000,9999),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'return_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'cashbox_id' => 'required|exists:cashboxes,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.return_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {

            $total = 0;

            foreach ($data['items'] as $item) {
                $total += $item['qty'] * $item['return_price'];
            }

            $salesReturn = SalesReturn::create([
                'return_number' => 'R-' . now()->format('Ymd') . '-' . rand(1000,9999),
                'customer_id' => $data['customer_id'],
                'return_date' => $data['return_date'],
                'total_amount' => $total,
                'cashbox_id' => $data['cashbox_id'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'return_price' => $item['return_price'],
                    'total' => $item['qty'] * $item['return_price'],
                ]);

                $product = Product::find($item['product_id']);

                // 🟢 رجوع للمخزون
                $before = $product->stock;
                $product->increment('stock', $item['qty']);

                // 🟡 حركة مخزون
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'return',
                    'quantity' => $item['qty'],
                    'before_qty' => $before,
                    'after_qty' => $product->stock,
                    'note' => 'مرتجع بيع',
                    'reference_type' => 'sales_return',
                    'reference_id' => $salesReturn->id,
                ]);
            }

            // 🔴 خصم من الخزنة
            app(CashboxService::class)->addTransaction(
                $data['cashbox_id'],
                'out',
                $total,
                'sales_return',
                $salesReturn->id,
                'مرتجع بيع'
            );
        });

        return redirect()->route('admin.sales_returns.index')
            ->with('success','تم تسجيل المرتجع بنجاح');
    }


}
