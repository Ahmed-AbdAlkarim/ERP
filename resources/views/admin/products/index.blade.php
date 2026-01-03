@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">المنتجات</h1>
            <p class="text-muted mb-0">إدارة المنتجات والمخزون</p>
        </div>
      
        <div>
            @can('create_product')
            <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>إضافة منتج جديد
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
                placeholder="ابحث بالاسم أو الباركود..."
            >
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
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>السعر</th>
                            <th>المخزون</th>
                            <th>حالة المخزون</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="products-body">
                        @forelse($products as $p)
                            <tr>
                                <td>
                                    @if($p->image)
                                        <img src="{{ asset('storage/'.$p->image) }}" width="50">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $p->name }}</td>
                                <td>
                                    @switch($p->type)
                                        @case('laptop') لابتوب @break
                                        @case('mobile') موبايل @break
                                        @case('security_camera') كاميرا مراقبة @break
                                        @case('photo_camera') كاميرا تصوير @break
                                        @case('accessory') إكسسوار @break
                                        @case('spare') قطعة غيار @break
                                        @case('service') خدمة @break
                                    @endswitch
                                </td>
                                <td>{{ number_format($p->selling_price,2) }} ج.م</td>
                                <td>{{ $p->stock }}</td>
                                <td>
                                    @if($p->isLowStock())
                                        <span class="badge bg-danger">قرب ينفد</span>
                                    @else
                                        <span class="badge bg-success">متوفر</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show',$p->id) }}" class="btn btn-info btn-sm">عرض</a>
                                    @can('view_inventory')
                                    <a href="{{ route('admin.inventory.card',$p->id) }}" class="btn btn-secondary btn-sm">حركة المنتج</a>
                                    @endcan
                                    @can('edit_product')
                                    <a href="{{ route('admin.products.edit',$p->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                    @endcan
                                    @can('delete_product')
                                    <form action="{{ route('admin.products.destroy',$p->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    لا توجد منتجات متاحة
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

function loadProducts(search = '', url = null) {
    $.ajax({
        url: url ?? "{{ route('admin.products.index') }}",
        type: "GET",
        data: { search: search },
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
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadProducts(search);
    }, 300);
});

// Pagination AJAX
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    loadProducts($('#search').val(), $(this).attr('href'));
});
</script>
@endsection
