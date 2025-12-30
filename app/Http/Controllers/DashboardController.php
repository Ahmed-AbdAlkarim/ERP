<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // إجمالي المبيعات اليوم
        $todaySales = SalesInvoice::whereDate('invoice_date', today())->sum('total');

        // صافي الربح: مجموع أرباح الفواتير اليومية (فقط إذا كان المستخدم لديه صلاحية عرض الربح)
        $netProfit = auth()->user()->can('view_profit') ?
            SalesInvoice::whereDate('invoice_date', today())->sum('profit') : null;


        // عدد الفواتير اليوم
        $todayInvoices = SalesInvoice::whereDate('invoice_date', today())->count();

        // تنبيهات المخزون (منتجات أقل من نقطة إعادة الطلب)
        $lowStockAlerts = Product::whereColumn('stock', '<=', 'reorder_level')->count();

        // المبيعات الشهرية لآخر 12 شهر
        $monthlySales = SalesInvoice::select(
            DB::raw('YEAR(invoice_date) as year'),
            DB::raw('MONTH(invoice_date) as month'),
            DB::raw('SUM(total) as total')
        )
        ->where('invoice_date', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // المبيعات اليومية للشهر الحالي
        $currentMonthSales = SalesInvoice::select(
            DB::raw('DAY(invoice_date) as day'),
            DB::raw('SUM(total) as total')
        )
        ->whereYear('invoice_date', now()->year)
        ->whereMonth('invoice_date', now()->month)
        ->groupBy('day')
        ->orderBy('day')
        ->get();

        // أكثر المنتجات مبيعاً
        $topProducts = SalesInvoiceItem::select('products.name', DB::raw('SUM(sales_invoice_items.qty) as total_quantity'))
            ->join('products', 'sales_invoice_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // آخر الفواتير
        $recentInvoices = SalesInvoice::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'netProfit',
            'todayInvoices',
            'lowStockAlerts',
            'monthlySales',
            'currentMonthSales',
            'topProducts',
            'recentInvoices'
        ));
    }
}
