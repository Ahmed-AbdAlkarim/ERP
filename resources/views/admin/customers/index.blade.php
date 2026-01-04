@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">العملاء</h1>
            <p class="text-muted mb-0">إدارة العملاء والمديونيات</p>
        </div>
        <div>
            @can('create_customer')
            <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>إضافة عميل جديد
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

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">قائمة العملاء</h6>
        </div>

        <!-- AJAX CONTENT -->
        <div id="customers-table">

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>رقم الهاتف</th>
                                <th>العنوان</th>
                               
                                @if(auth()->user()->can('show_customer_debts'))
                                <th>المديونية</th>
                                @endif
                                <th>الرصيد</th>
                                @if(auth()->user()->can('show_customer_details') || auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer'))
                                    <th>إجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td class="fw-bold">{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->address }}</td>
                                    @if(auth()->user()->can('show_customer_debts'))
                                    <td class="fw-bold text-danger">
                                        @can('show_customer_debts')
                                        {{ number_format($customer->debt, 2) }} ج.م
                                        @endcan
                                    </td>
                                    @endif

                                    <td class="fw-bold text-success">
                                        {{ number_format($customer->balance, 2) }} ج.م
                                    </td>
                                    
                                    @if(auth()->user()->can('show_customer_details') || auth()->user()->can('edit_customer') || auth()->user()->can('delete_customer'))
                                    <td>
                                        @can('show_customer_details')
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-info btn-sm">عرض</a>
                                        @endcan
                                        @can('edit_customer')
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                        @endcan
                                        @can('delete_customer')
                                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        لا يوجد نتائج
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($customers->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $customers->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let timer;

// Live Search
$(document).on('keyup', '#search', function () {
    let search = $(this).val();

    clearTimeout(timer);
    timer = setTimeout(function () {
        loadCustomers(search);
    }, 300);
});

// Pagination AJAX
$(document).on('click', '.pagination a', function (e) {
    e.preventDefault();
    loadCustomers($('#search').val(), $(this).attr('href'));
});

function loadCustomers(search = '', url = null) {
    $.ajax({
        url: url ?? "{{ route('admin.customers.index') }}",
        type: "GET",
        data: { search: search },
        success: function (data) {
            $('#customers-table').html(data);
        }
    });
}
</script>

@endsection
