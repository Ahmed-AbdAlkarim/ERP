@extends('layouts.master')

@section('title', 'تفاصيل أرباح المبيعات')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تفاصيل أرباح المبيعات</h1>
            <p class="text-muted mb-0">عرض تفاصيل أرباح المبيعات</p>
        </div>
        <div>
            <a href="{{ route('admin.profit-reports.index', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للتقارير
            </a>
        </div>
    </div>
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4" id="filters-container" data-date-from="{{ $dateFrom }}" data-date-to="{{ $dateTo }}">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <label for="search" class="form-label">البحث</label>
                    <input type="text" class="form-control" id="search" placeholder="رقم الفاتورة، العميل، البائع...">
                </div>
                <div class="col-md-2">
                    <label for="profit_from" class="form-label">ربح من</label>
                    <input type="number" class="form-control" id="profit_from" placeholder="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <label for="profit_to" class="form-label">ربح إلى</label>
                    <input type="number" class="form-control" id="profit_to" placeholder="0" step="0.01">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" id="reset-btn" class="btn btn-secondary w-100">
                        إعادة تعيين
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Container -->
    <div id="sales-container">
        <!-- Sales Profits Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">أرباح المبيعات</h5>
            </div>

            <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light">
                <tr>
                    <th style="width:15%">رقم الفاتورة</th>
                    <th style="width:12%" class="text-center">التاريخ</th>
                    <th style="width:18%">العميل</th>
                    <th style="width:18%">البائع</th>
                    <th style="width:15%" class="text-end">إجمالي الفاتورة</th>
                    <th style="width:15%" class="text-end">إجمالي الربح</th>
                    <th style="width:7%" class="text-center">الإجراءات</th>
                </tr>
            </thead>

            <tbody id="sales-body">
                @forelse($salesDetails as $invoice)
                    <tr>
                        <td class="fw-semibold">
                            {{ $invoice->invoice_number }}
                        </td>

                        <td class="text-center text-muted">
                            {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}
                        </td>

                        <td class="text-truncate" style="max-width: 150px;"
                            title="{{ $invoice->customer->name ?? 'غير محدد' }}">
                            {{ $invoice->customer->name ?? 'غير محدد' }}
                        </td>

                        <td class="text-truncate" style="max-width: 150px;"
                            title="{{ $invoice->user->name ?? 'غير محدد' }}">
                            {{ $invoice->user->name ?? 'غير محدد' }}
                        </td>

                        <td class="text-end fw-semibold text-primary">
                            {{ number_format($invoice->total, 2) }} ج.م
                        </td>

                        <td class="text-end fw-semibold text-success">
                            {{ number_format($invoice->profit, 2) }} ج.م
                        </td>

                        <td class="text-center">
                            <a href="{{ route('admin.sales-invoices.show', $invoice) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <div class="mt-1">لا توجد فواتير بيع</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let timer;

function loadSales() {
    const search = $('#search').val();
    const profitFrom = $('#profit_from').val();
    const profitTo = $('#profit_to').val();
    const dateFrom = $('#date_from').val();
    const dateTo = $('#date_to').val();

    $.ajax({
        url: "{{ route('admin.profit-reports.sales') }}",
        type: "GET",
        data: {
            search: search,
            profit_from: profitFrom,
            profit_to: profitTo,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function (data) {
            const html = $('<div>').html(data);
            const salesBody = html.find('#sales-body').html();

            // Update table body
            $('#sales-body').html(salesBody);
        }
    });
}

// Live Search
$(document).on('keyup', '#search', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadSales();
    }, 300);
});

// Live Filter on profit change
$(document).on('input', '#profit_from, #profit_to', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadSales();
    }, 300);
});

// Live Filter on date change
$(document).on('change', '#date_from, #date_to', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadSales();
    }, 300);
});

// Filter on button click
$(document).on('click', '#filter-btn', function () {
    loadSales();
});

// Reset button click
$(document).on('click', '#reset-btn', function () {
    const defaultDateFrom = $('#filters-container').data('date-from');
    const defaultDateTo = $('#filters-container').data('date-to');

    $('#search').val('');
    $('#profit_from').val('');
    $('#profit_to').val('');
    $('#date_from').val(defaultDateFrom);
    $('#date_to').val(defaultDateTo);
    loadSales();
});

// Filter on Enter key
$(document).on('keypress', '#search, #profit_from, #profit_to, #date_from, #date_to', function (e) {
    if (e.which === 13) {
        loadSales();
    }
});
</script>
@endsection
