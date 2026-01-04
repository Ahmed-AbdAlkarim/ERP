@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تفاصيل المصاريف</h1>
            <p class="text-muted mb-0">عرض تفاصيل مصاريف الشراء والمصاريف العامة</p>
        </div>
        <div>
            <a href="{{ route('admin.profit-reports.index', request()->only(['date_from', 'date_to'])) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للتقرير
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4" id="filters-container" data-date-from="{{ $dateFrom }}" data-date-to="{{ $dateTo }}">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="search" class="form-label">البحث</label>
                    <input type="text" class="form-control" id="search" placeholder="ابحث بالفاتورة أو المورد أو العنوان...">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" id="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" id="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="button" id="filter-btn" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-2"></i>فلترة
                        </button>
                        <button type="button" id="reset-btn" class="btn btn-secondary flex-fill">
                            <i class="fas fa-undo me-2"></i>إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Container -->
    <div id="expenses-container">
        <!-- Purchase Expenses Table -->
        <div class="card border-0 shadow-sm mb-4" id="purchase-expenses-card">
            <div class="card-header bg-white">
                <h5 class="mb-0">مصاريف الشراء</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>المورد</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody id="purchase-expenses-body">
                            @forelse($purchaseExpensesDetails as $expense)
                                <tr>
                                    <td>{{ $expense->invoice_number }}</td>
                                    <td>{{ $expense->date }}</td>
                                    <td>{{ $expense->supplier ? $expense->supplier->name : 'غير محدد' }}</td>
                                    <td>{{ number_format($expense->additional_expenses, 2) }} ج.م</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>
                                        <br>
                                        لا توجد مصاريف شراء
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- General Expenses Table -->
        <div class="card border-0 shadow-sm" id="general-expenses-card">
            <div class="card-header bg-white">
                <h5 class="mb-0">المصاريف العامة</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>العنوان</th>
                                <th>الفئة</th>
                                <th>التاريخ</th>
                                <th>الخزنة</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody id="general-expenses-body">
                            @forelse($generalExpensesDetails as $expense)
                                <tr>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ $expense->category }}</td>
                                    <td>{{ $expense->expense_date }}</td>
                                    <td>{{ $expense->cashbox ? $expense->cashbox->name : 'غير محدد' }}</td>
                                    <td>{{ number_format($expense->amount, 2) }} ج.م</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <br>
                                        لا توجد مصاريف عامة
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

function loadExpenses() {
    const search = $('#search').val();
    const dateFrom = $('#date_from').val();
    const dateTo = $('#date_to').val();

    $.ajax({
        url: "{{ route('admin.profit-reports.expenses') }}",
        type: "GET",
        data: {
            search: search,
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function (data) {
            const html = $('<div>').html(data);
            const purchaseBody = html.find('#purchase-expenses-body').html();
            const generalBody = html.find('#general-expenses-body').html();

            // Update table bodies
            $('#purchase-expenses-body').html(purchaseBody);
            $('#general-expenses-body').html(generalBody);

            // Reorder tables based on search results
            reorderTables(purchaseBody, generalBody);
        }
    });
}

function reorderTables(purchaseBody, generalBody) {
    const hasPurchaseResults = purchaseBody.includes('<tr>') && !purchaseBody.includes('لا توجد مصاريف شراء');
    const hasGeneralResults = generalBody.includes('<tr>') && !generalBody.includes('لا توجد مصاريف عامة');

    const container = $('#expenses-container');
    const purchaseCard = $('#purchase-expenses-card');
    const generalCard = $('#general-expenses-card');

    // If both have results or both are empty, keep original order
    if ((hasPurchaseResults && hasGeneralResults) || (!hasPurchaseResults && !hasGeneralResults)) {
        return;
    }

    // If only purchase has results, move it to top
    if (hasPurchaseResults && !hasGeneralResults) {
        purchaseCard.removeClass('mb-4').addClass('mb-4');
        generalCard.removeClass('mb-4');
        container.prepend(generalCard);
        container.prepend(purchaseCard);
    }
    // If only general has results, move it to top
    else if (!hasPurchaseResults && hasGeneralResults) {
        generalCard.removeClass('mb-4').addClass('mb-4');
        purchaseCard.removeClass('mb-4');
        container.prepend(purchaseCard);
        container.prepend(generalCard);
    }
}

// Live Search
$(document).on('keyup', '#search', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadExpenses();
    }, 300);
});

// Live Filter on date change
$(document).on('change', '#date_from, #date_to', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadExpenses();
    }, 300);
});

// Filter on button click
$(document).on('click', '#filter-btn', function () {
    loadExpenses();
});

// Reset button click
$(document).on('click', '#reset-btn', function () {
    const defaultDateFrom = $('#filters-container').data('date-from');
    const defaultDateTo = $('#filters-container').data('date-to');

    $('#search').val('');
    $('#date_from').val(defaultDateFrom);
    $('#date_to').val(defaultDateTo);
    loadExpenses();
});

// Filter on Enter key
$(document).on('keypress', '#search, #date_from, #date_to', function (e) {
    if (e.which === 13) {
        loadExpenses();
    }
});
</script>
@endsection
