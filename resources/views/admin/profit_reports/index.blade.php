@extends('layouts.master')

@section('title', 'تقارير الأرباح')

@section('content')

<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">تقارير الأرباح</h1>
            <p class="text-muted mb-0">عرض تقارير الأرباح والمصاريف</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.profit-reports.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bx bx-search me-1"></i>عرض التقرير</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                    <h5 class="card-title">إجمالي المبيعات</h5>
                    <h3 class="fw-bold">{{ number_format($totalSales, 2) }} ج.م</h3>
                </div>  
            </div>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.profit-reports.sales', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="text-decoration-none">
                <div class="card bg-success text-white shadow h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                        <h5 class="card-title">إجمالي أرباح المبيعات</h5>
                        <h3 class="fw-bold">{{ number_format($totalSalesProfit, 2) }} ج.م</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.profit-reports.expenses', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="text-decoration-none">
                <div class="card bg-danger text-white shadow h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-minus-circle fa-2x mb-3"></i>
                        <h5 class="card-title">إجمالي المصاريف</h5>
                        <h3 class="fw-bold">{{ number_format($totalExpenses, 2) }} ج.م</h3>
                        <small>مصاريف الشراء: {{ number_format($purchaseExpenses ?? 0, 2) }} ج.م</small><br>
                        <small>مصاريف عامة: {{ number_format($generalExpenses ?? 0, 2) }} ج.م</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-5 g-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice fa-2x mb-3"></i>
                    <h5 class="card-title">عدد فواتير البيع</h5>
                    <h3 class="fw-bold">{{ $totalSalesInvoices }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice-dollar fa-2x mb-3"></i>
                    <h5 class="card-title">عدد فواتير الشراء</h5>
                    <h3 class="fw-bold">{{ $totalPurchaseInvoices }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-2x mb-3"></i>
                    <h5 class="card-title">صافي الربح</h5>
                    <h3 class="fw-bold">{{ number_format($netProfit, 2) }} ج.م</h3>
                </div>
            </div>
        </div>
    </div>

               
</div>
@endsection
