<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitReportsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'type', 'seller_id', 'product_id', 'supplier_id', 'payment_method']);

        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        $salesQuery = SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo]);

        if ($filters['seller_id'] ?? null) {
            $salesQuery->where('user_id', $filters['seller_id']);
        }

        if ($filters['payment_method'] ?? null) {
            $salesQuery->where('payment_method', $filters['payment_method']);
        }

        $totalSalesProfit = $salesQuery->sum('profit');

        $totalSales = $salesQuery->sum('total');

        $totalSalesInvoices = $salesQuery->count();

        $purchasesQuery = PurchaseInvoice::whereBetween('date', [$dateFrom, $dateTo]);

        if ($filters['supplier_id'] ?? null) {
            $purchasesQuery->where('supplier_id', $filters['supplier_id']);
        }

        $purchaseExpenses = $purchasesQuery->sum('additional_expenses');

        $totalPurchaseInvoices = $purchasesQuery->count();

        $generalExpensesQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo]);
        $generalExpenses = $generalExpensesQuery->sum('amount');

        $totalExpenses = $purchaseExpenses + $generalExpenses;

        $netProfit = $totalSalesProfit - $totalExpenses;

        $profitsBySeller = [];
        $profitsByProduct = [];
        $profitsBySupplier = [];
        $profitsByPaymentType = [];

        if (($filters['type'] ?? null) == 'seller') {
            $profitsBySeller = SalesInvoice::select('user_id', DB::raw('SUM(profit) as total_profit'))
                ->whereBetween('invoice_date', [$dateFrom, $dateTo])
                ->groupBy('user_id')
                ->with('user')
                ->get();
        }

        if (($filters['type'] ?? null) == 'product') {
            $profitsByProduct = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->join('products', 'sales_invoice_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(sales_invoice_items.profit) as total_profit'))
                ->whereBetween('sales_invoices.invoice_date', [$dateFrom, $dateTo])
                ->groupBy('products.id', 'products.name')
                ->get();
        }

        if (($filters['type'] ?? null) == 'supplier') {
            $profitsBySupplier = DB::table('purchase_invoice_items')
                ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->join('suppliers', 'purchase_invoices.supplier_id', '=', 'suppliers.id')
                ->select('suppliers.name', DB::raw('SUM(purchase_invoice_items.quantity * purchase_invoice_items.purchase_price) as total_cost'))
                ->whereBetween('purchase_invoices.date', [$dateFrom, $dateTo])
                ->groupBy('suppliers.id', 'suppliers.name')
                ->get();
        }

        if (($filters['type'] ?? null) == 'payment') {
            $profitsByPaymentType = SalesInvoice::select('payment_method', DB::raw('SUM(profit) as total_profit'))
                ->whereBetween('invoice_date', [$dateFrom, $dateTo])
                ->groupBy('payment_method')
                ->get();
        }

        return view('admin.profit_reports.index', compact(
            'totalSales', 'totalSalesProfit', 'totalExpenses', 'netProfit',
            'purchaseExpenses', 'generalExpenses', 'totalSalesInvoices', 'totalPurchaseInvoices',
            'profitsBySeller', 'profitsByProduct', 'profitsBySupplier', 'profitsByPaymentType',
            'filters', 'dateFrom', 'dateTo'
        ));
    }

    public function expenses(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'search']);

        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        $purchaseExpensesQuery = PurchaseInvoice::whereBetween('date', [$dateFrom, $dateTo])
            ->where('additional_expenses', '>', 0)
            ->with('supplier');

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $purchaseExpensesQuery->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $purchaseExpensesDetails = $purchaseExpensesQuery->get();

        $generalExpensesQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->with('safe');

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $generalExpensesQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $generalExpensesDetails = $generalExpensesQuery->get();

        return view('admin.profit_reports.expenses', compact(
            'purchaseExpensesDetails', 'generalExpensesDetails',
            'dateFrom', 'dateTo'
        ));
    }

    public function sales(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'search', 'profit_from', 'profit_to']);

        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();

        $salesQuery = SalesInvoice::whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->where('profit', '>', 0)
            ->with('customer', 'user');

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $salesQuery->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($filters['profit_from'] ?? null) {
            $salesQuery->where('profit', '>=', $filters['profit_from']);
        }

        if ($filters['profit_to'] ?? null) {
            $salesQuery->where('profit', '<=', $filters['profit_to']);
        }

        $salesDetails = $salesQuery->get();

        return view('admin.profit_reports.sales', compact(
            'salesDetails',
            'dateFrom', 'dateTo'
        ));
    }
}
