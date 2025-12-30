@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">فواتير الشراء</h1>
            <p class="text-muted mb-0">إدارة فواتير الشراء والمشتريات</p>
        </div>
        <div>
            @can('create_purchase_invoice')
            <a href="{{ route('admin.purchase_invoices.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>فاتورة شراء جديدة
            </a>
            @endcan
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <input
                type="text"
                id="search"
                class="form-control"
                placeholder="ابحث برقم الفاتورة أو اسم المورد..."
            >
        </div>
    </div>

    <!-- Purchase Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">قائمة فواتير الشراء</h6>
        </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>المورد</th>
                                <th>التاريخ</th>
                                <th>إجمالي التكلفة</th>
                                <th>الحالة</th>
                                @if(auth()->user()->can('show_purchase_invoice_details') || auth()->user()->can('edit_purchase_invoice') || auth()->user()->can('delete_purchase_invoice'))
                                <th>إجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="purchase-invoices-body">
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->supplier->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                                    <td class="fw-bold text-success">{{ number_format($invoice->total_cost, 2) }} ج.م</td>
                                    <td>
                                        @switch($invoice->payment_status)
                                            @case('paid')
                                                <span class="badge bg-success">مدفوع</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-warning text-dark">دفع جزئي</span>
                                                @break
                                            @case('due')
                                                <span class="badge bg-danger">أجل</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">غير محدد</span>
                                        @endswitch
                                    </td>
                                    @if(auth()->user()->can('show_purchase_invoice_details') || auth()->user()->can('edit_purchase_invoice') || auth()->user()->can('delete_purchase_invoice'))
                                    <td>
                                        @can('show_purchase_invoice_details')
                                        <a href="{{ route('admin.purchase_invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">عرض</a>
                                        @endcan
                                        @can('edit_purchase_invoice')
                                        <a href="{{ route('admin.purchase_invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                        @endcan
                                        @can('delete_purchase_invoice')
                                        <form action="{{ route('admin.purchase_invoices.destroy', $invoice->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>
                                        <br>الفاتورة التي تبحث عنها غير موجودة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($invoices->hasPages())
                <div class="card-footer bg-white border-0" id="pagination-links">
                    {{ $invoices->links() }}
                </div>
            @endif
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let timer;

function loadPurchaseInvoices(search = '', url = null) {
    $.ajax({
        url: url ?? "{{ route('admin.purchase_invoices.index') }}",
        type: "GET",
        data: { search: search },
        success: function (data) {
            const html = $('<div>').html(data);
            $('#purchase-invoices-body').html(html.find('#purchase-invoices-body').html());
            $('#pagination-links').html(html.find('#pagination-links').html());
        }
    });
}

// Live Search
$(document).on('keyup', '#search', function () {
    let search = $(this).val();
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadPurchaseInvoices(search);
    }, 300);
});

// Pagination AJAX
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    loadPurchaseInvoices($('#search').val(), $(this).attr('href'));
});
</script>

@endsection
