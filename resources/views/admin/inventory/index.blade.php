@extends('layouts.master')

@section('title', 'إدارة المخزون')

@section('content')

<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">إدارة المخزون</h1>
            <p class="text-muted mb-0">قائمة المنتجات وإدارة المخزون</p>
        </div>
        <div>
            <a href="{{ route('admin.inventory.low_stock') }}" class="btn btn-warning rounded-pill px-4 me-2">
                <i class="bx bx-show me-1"></i>منخفضة المخزون
            </a>
            <a href="{{ route('admin.inventory.adjusted') }}" class="btn btn-success rounded-pill px-4">
                <i class="bx bx-check me-1"></i>المنتجات المسوية
            </a>
            <a href="{{ route('admin.inventory.export.excel') }}"
   class="btn btn-secondary rounded-pill px-4 ms-2">
    <i class="bx bx-download me-1"></i>تصدير جرد إكسل
</a>

            <a href="{{ route('admin.inventory.excel.pending') }}" class="btn btn-info rounded-pill px-4 ms-2">
                <i class="bx bx-upload me-1"></i>استيراد جرد من إكسل
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الرمز...">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">حالة المخزون</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>منخفض المخزون</option>
                                <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>مخزون طبيعي</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                                <i class="bx bx-reset me-1"></i>إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">قائمة المنتجات</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-bold">#</th>
                            <th class="border-0 fw-bold">المنتج</th>
                            <th class="border-0 fw-bold">المخزون</th>
                            <th class="border-0 fw-bold">نقطة إعادة الطلب</th>
                            <th class="border-0 fw-bold">الحالة</th>
                            <th class="border-0 fw-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="products-body">
                        @forelse($products as $index => $p)
                        <tr>
                            <td class="fw-bold text-primary">{{ $products->firstItem() + $index }}</td>
                            <td class="fw-bold">{{ $p->name }}</td>
                            <td>{{ $p->stock }}</td>
                            <td>{{ $p->reorder_level }}</td>
                            <td>
                                @if($p->isLowStock())
                                    <span class="badge bg-danger rounded-pill d-inline-block text-center" style="width: 70px;">منخفض</span>
                                @else
                                    <span class="badge bg-success rounded-pill d-inline-block text-center" style="width: 70px;">طبيعي</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.inventory.card', $p->id) }}" class="btn btn-sm btn-info rounded-pill px-3">
                                    <i class="bx bx-show me-1"></i>عرض الكارت
                                </a>
                                <a href="{{ route('admin.inventory.adjust_form', $p->id) }}" class="btn btn-sm btn-warning rounded-pill px-3">
                                    <i class="bx bx-edit me-1"></i>تسوية
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <br> المنتج الذي تبحث عنه غير موجود في المخزون
                            </td>

                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-0" id="pagination-links">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let timer;

function loadProducts(search = '', status = '', url = null) {
    $.ajax({
        url: url ?? "{{ route('admin.inventory.index') }}",
        type: "GET",
        data: { search: search, status: status },
        success: function (data) {
            const html = $('<div>').html(data);
            $('#products-body').html(html.find('#products-body').html());
            $('#pagination-links').html(html.find('#pagination-links').html());
        }
    });
}

// Live Search
$(document).on('keyup', '#search', function () {
    let search = $(this).val();
    let status = $('#status').val();
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadProducts(search, status);
    }, 300);
});

// Status Filter Change
$(document).on('change', '#status', function () {
    let search = $('#search').val();
    let status = $(this).val();
    loadProducts(search, status);
});

// Pagination AJAX
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    let search = $('#search').val();
    let status = $('#status').val();
    loadProducts(search, status, $(this).attr('href'));
});
</script>

@endsection
