@extends('layouts.master')
@section('title', 'لوحة التحكم')

@section('content')

<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="mb-4 p-4 rounded shadow text-white"
        style="
            min-height:120px;
            background: linear-gradient(135deg, #0f766e, #134e4a);
        ">
        <div class="d-flex align-items-center">
            <div class="me-3 d-flex align-items-center justify-content-center"
                style="
                    width:60px;
                    height:60px;
                    border-radius:16px;
                    background: rgba(255,255,255,0.18);
                    font-size:26px;
                ">
                <i class="fas fa-tachometer-alt"></i>
            </div>

            <div>
                <h1 class="h3 mb-1 fw-bold" style="color:#ffffff;">
                    لوحة التحكم
                </h1>   
                <p class="mb-0 opacity-75">نظرة عامة على أداء المبيعات والمخزون</p>
            </div>
        </div>
    </div>


    <!-- Stats Cards -->
    <div class="row g-3 mb-4">

        <!-- Sales -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow h-100 text-white"
                 style="min-height:140px;
                 background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-dollar-sign fa-2x me-3 opacity-75"></i>
                    <div>
                        <h6 class="mb-1">إجمالي المبيعات اليوم</h6>
                        <h5 class="mb-0 fw-bold">{{ number_format($todaySales, 2) }} ج.م</h5>
                        <small class="opacity-75">مبيعات اليوم</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit -->
        <div class="col-xl-3 col-md-6 col-12">
            <a href="{{ route('admin.profit-reports.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow h-100 text-white"
                     style="min-height:140px;
                     background: linear-gradient(135deg, #11998e, #38ef7d);">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-money-bill-wave fa-2x me-3 opacity-75"></i>
                        <div>
                            <h6 class="mb-1">الأرباح اليومية</h6>
                            <h5 class="mb-0 fw-bold">{{ number_format($netProfit, 2) }} ج.م</h5>
                            <small class="opacity-75">إجمالي ربح اليوم</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Invoices -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow h-100 text-white"
                 style="min-height:140px;
                 background: linear-gradient(135deg, #7f00ff, #e100ff);">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar fa-2x me-3 opacity-75"></i>
                    <div>
                        <h6 class="mb-1">عدد الفواتير</h6>
                        <h5 class="mb-0 fw-bold">{{ $todayInvoices }}</h5>
                        <small class="opacity-75">فواتير اليوم</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow h-100 text-white"
                 style="min-height:140px;
                 background: linear-gradient(135deg, #cb2d3e, #ef473a);">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 opacity-75"></i>
                    <div>
                        <h6 class="mb-1">تنبيهات المخزون</h6>
                        <h5 class="mb-0 fw-bold">{{ $lowStockAlerts }}</h5>
                        <small class="opacity-75">منتجات قاربت على النفاد</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts Section -->
    <div class="row g-3 mb-4 align-items-stretch">

        <div class="col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>المبيعات الشهرية</h6>
                    <select class="form-select form-select-sm w-auto bg-white" id="salesPeriodSelect">
                        <option value="month" selected>الشهر الحالي</option>
                        <option value="6months">آخر 6 أشهر</option>
                        <option value="year">آخر سنة</option>
                    </select>
                </div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>أكثر المنتجات مبيعاً</h6>
                </div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Invoices -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">آخر الفواتير</h6>
            <a href="{{ route('admin.sales-invoices.index') }}" class="btn btn-primary btn-sm">عرض الكل</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>العميل</th>
                            <th>المنتج</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name ?? 'غير محدد' }}</td>
                            <td>{{ $invoice->items->first()->product->name ?? 'غير محدد' }}</td>
                            <td>{{ number_format($invoice->total, 2) }} ج.م</td>
                            <td>
                                @switch($invoice->status)
                                    @case('paid') <span class="badge bg-success">مدفوع</span> @break
                                    @case('partial') <span class="badge bg-warning text-white">دفع جزئي</span> @break
                                    @case('pending') <span class="badge bg-danger">آجل</span> @break
                                    @default <span class="badge bg-secondary">غير محدد</span>
                                @endswitch
                            </td>
                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">لا توجد فواتير</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlySalesData = @json($monthlySales);
    const currentMonthSalesData = @json($currentMonthSales);

    const sixMonthsData = monthlySalesData.slice(-6);
    const sixMonthsLabels = sixMonthsData.map(i => `${i.year}-${i.month.toString().padStart(2,'0')}`);
    const sixMonthsTotals = sixMonthsData.map(i => parseFloat(i.total));

    const monthLabels = currentMonthSalesData.map(i => i.day);
    const monthTotals = currentMonthSalesData.map(i => parseFloat(i.total));

    const ctxMonthly = document.getElementById('monthlySalesChart');
    let monthlyChart = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'المبيعات',
                data: monthTotals,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    document.getElementById('salesPeriodSelect').addEventListener('change', function () {
        if (this.value === 'month') {
            monthlyChart.data.labels = monthLabels;
            monthlyChart.data.datasets[0].data = monthTotals;
        } else if (this.value === '6months') {
            monthlyChart.data.labels = sixMonthsLabels;
            monthlyChart.data.datasets[0].data = sixMonthsTotals;
        } else if (this.value === 'year') {
            const yearLabels = monthlySalesData.map(i => `${i.year}-${i.month.toString().padStart(2,'0')}`);
            const yearTotals = monthlySalesData.map(i => parseFloat(i.total));
            monthlyChart.data.labels = yearLabels;
            monthlyChart.data.datasets[0].data = yearTotals;
        }
        monthlyChart.update();
    });

    const topProductsData = @json($topProducts);
    new Chart(document.getElementById('topProductsChart'), {
        type: 'pie',
        data: {
            labels: topProductsData.map(i => i.name),
            datasets: [{
                data: topProductsData.map(i => i.total_quantity)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

@endsection
