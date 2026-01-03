<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use App\Notifications\LowStockNotification;
use App\Models\User;
use App\Exports\InventoryStockExport;
use Maatwebsite\Excel\Facades\Excel;


class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->whereColumn('stock', '<=', 'reorder_level');
            } elseif ($request->status === 'normal') {
                $query->whereColumn('stock', '>', 'reorder_level');
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(50);

        $lowStockProducts = Product::whereColumn('stock', '<=', 'reorder_level')
            ->orderBy('stock','asc')
            ->take(10)
            ->get();

        if ($request->ajax()) {
            return view('admin.inventory.index', compact('products', 'lowStockProducts'))->render();
        }

        return view('admin.inventory.index', compact('products', 'lowStockProducts'));
    }

    
    public function card($productId)
    {
        $product = Product::findOrFail($productId);

        // 1️⃣ تسويات المخزون
        $adjustments = $product->stockMovements()
            ->where(function ($q) {
                $q->where('type', 'adjustment')
                ->orWhere('type', '');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'adjustments_page');


        // 2️⃣ وارد المنتج (مشتريات)
        $purchases = \App\Models\PurchaseInvoiceItem::with(['invoice.supplier'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'purchases_page');

        // 3️⃣ صادر المنتج (مبيعات)
        $sales = \App\Models\SalesInvoiceItem::with(['invoice.customer'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sales_page');

        // 4️⃣ مرتجعات المنتج ✅ (ده 1.1)
        $returns = \App\Models\SalesReturnItem::with('salesReturn')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'returns_page');

        return view(
            'admin.inventory.card',
            compact('product', 'adjustments', 'purchases', 'sales', 'returns')
        );
    }




  
    public function showAdjustForm($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.inventory.adjust', compact('product'));
    }

    public function exportInventoryExcel(Request $request)
    {
        $type = $request->get('type'); // ممكن null

        return Excel::download(
            new InventoryStockExport($type),
            'inventory_stock_' . ($type ?? 'all') . '_' . now()->format('Y-m-d') . '.xlsx'
        );
    }



    
    public function adjust(Request $request, $productId)
    {
        $data = $request->validate([
            'actual_qty' => 'required|integer|min:0',
            'reason'     => 'nullable|string|max:1000',
            'reason_text' => 'nullable|string|max:2000'
        ]);

        $product = Product::findOrFail($productId);

       
        $combinedReason = $data['reason'];
        if (!empty($data['reason_text'])) {
            $combinedReason .= (!empty($combinedReason) ? ' - ' : '') . $data['reason_text'];
        }

        DB::transaction(function() use($product, $data, $combinedReason) {

            $product->adjustStock((int)$data['actual_qty'], $combinedReason ?: null, auth()->id());

          
            $product->refresh();
            if ($product->isLowStock()) {
                $last = $product->stock_alert_sent_at;
                if (!$last || $last->lt(now()->subHours(24))) {
                    $product->update(['stock_alert_sent_at' => now()]);
                }
            }
        });

        return redirect()->route('admin.inventory.card', $productId)->with('success','تمت تسوية المخزون بنجاح');
    }


    public function lowStock()
    {
        $products = Product::whereColumn('stock', '<=', 'reorder_level')
            ->orderBy('stock','asc')
            ->paginate(50);

        return view('admin.inventory.low_stock', compact('products'));
    }


    public function adjustedProducts()
    {
        $products = Product::whereHas('stockMovements', function($query) {
            $query->where('type', 'adjustment');
        })->with(['stockMovements' => function($query) {
            $query->where('type', 'adjustment')->latest()->take(1);
        }])->paginate(50);

        return view('admin.inventory.adjusted', compact('products'));
    }

    public function report(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->endOfMonth()->toDateString());

        $movements = StockMovement::with('product')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at','desc')
            ->paginate(100);

        return view('admin.inventory.report', compact('movements','from','to'));
    }
}
