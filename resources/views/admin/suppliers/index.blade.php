@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">الموردين</h1>
            <p class="text-muted mb-0">إدارة الموردين والمديونيات</p>
        </div>
        <div>
            @can('create_supplier')
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>إضافة مورد جديد
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
                placeholder="ابحث بالاسم أو رقم الهاتف..."
            >
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">قائمة الموردين</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>الشركة</th>
                            @if(auth()->user()->can('show_supplier_debts'))
                            <th>المديونية</th>
                            @endif
                            <th>آخر توريد</th>
                            @if(auth()->user()->can('show_supplier_details')||auth()->user()->can('edit_supplier')||auth()->user()->can('delete_supplier'))
                            <th>إجراءات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="suppliers-body">
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="fw-bold">{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->company }}</td>
                                @if(auth()->user()->can('show_supplier_debts'))
                                <td class="fw-bold text-danger">
                                    @can('show_supplier_debts')
                                    {{ number_format($supplier->debt, 2) }} ج.م
                                    @endcan
                                </td>
                                @endif
                                <td>
                                    {{ $supplier->last_supply_date
                                        ? \Carbon\Carbon::parse($supplier->last_supply_date)->format('d/m/Y')
                                        : '—'
                                    }}
                                </td>
                                @if(auth()->user()->can('show_supplier_details')||auth()->user()->can('edit_supplier')||auth()->user()->can('delete_supplier'))
                                <td>
                                    @can('show_supplier_details')
                                    <a href="{{ route('admin.suppliers.show', $supplier->id) }}" class="btn btn-info btn-sm">عرض</a>
                                    @endcan
                                    @can('edit_supplier')
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                    @endcan
                                    @can('delete_supplier')
                                    <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @empty
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-dolly fa-2x mb-2"></i>
                                <br> المورد الذي تبحث عنه غير موجود
                            </td>

                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($suppliers->hasPages())
            <div class="card-footer bg-white border-0" id="pagination-links">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let timer;

function loadSuppliers(search = '', url = null) {
    $.ajax({
        url: url ?? "{{ route('admin.suppliers.index') }}",
        type: "GET",
        data: { search: search },
        success: function (data) {
            const html = $('<div>').html(data);
            $('#suppliers-body').html(html.find('#suppliers-body').html());
            $('#pagination-links').html(html.find('#pagination-links').html());
        }
    });
}

// Live Search
$(document).on('keyup', '#search', function () {
    let search = $(this).val();
    clearTimeout(timer);
    timer = setTimeout(function () {
        loadSuppliers(search);
    }, 300);
});

// Pagination AJAX
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    loadSuppliers($('#search').val(), $(this).attr('href'));
});
</script>

@endsection
