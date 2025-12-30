@extends('layouts.master')

@section('title', 'إدارة المصروفات')

@section('content')
<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">إدارة المصروفات</h1>
            <p class="text-muted mb-0">عرض وإدارة جميع المصروفات</p>
        </div>
        <div>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>إضافة مصروف جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">البحث</label>
                    <input type="text" class="form-control" name="search" placeholder="البحث بالعنوان...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">الفئة</label>
                    <select class="form-select" name="category">
                        <option value="">جميع الفئات</option>
                        <option value="electricity">كهرباء</option>
                        <option value="rent">إيجار</option>
                        <option value="salaries">مرتبات</option>
                        <option value="shipping">شحن</option>
                        <option value="maintenance">صيانة</option>
                        <option value="marketing">تسويق</option>
                        <option value="office">مكتب</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" class="form-control" name="start_date">
                </div>

                <div class="col-md-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" class="form-control" name="end_date">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="reset-btn" class="btn btn-outline-secondary w-100">
                        إعادة تعيين
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm">
        <div id="table-container" class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>الفئة</th>
                            <th>المبلغ</th>
                            <th>الخزنة</th>
                            <th>التاريخ</th>
                            <th>المرفق</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $expenses->firstItem() + $index }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->category }}</td>
                            <td>{{ number_format($expense->amount,2) }} ج.م</td>
                            <td>{{ $expense->cashbox?->name ?? 'غير محدد' }}</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>
                                @if($expense->attachment)
                                    <a href="{{ $expense->attachment_url }}" target="_blank">عرض</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.expenses.edit',$expense) }}" class="btn btn-sm btn-primary">
                                    تعديل
                                </a>
                                <form action="{{ route('admin.expenses.destroy',$expense) }}" method="POST" class="d-inline-block"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                لا توجد مصروفات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
            <div class="p-3">
                {!! $expenses->appends(request()->query())->links() !!}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- AJAX --}}
<script>
$(document).ready(function () {

    let timer = null;

    function fetchData(url = "{{ route('admin.expenses.index') }}") {
        $.ajax({
            url: url,
            type: "GET",
            data: $('#filter-form').serialize(),
            success: function (response) {
                let table = $(response).find('#table-container').html();
                $('#table-container').html(table);
            }
        });
    }

    // 🔥 بحث لحظي
    $('input[name="search"]').on('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            fetchData();
        }, 300);
    });

    // باقي الفلاتر
    $('#filter-form select, #filter-form input[type="date"]').on('change', function () {
        fetchData();
    });

    // Reset
    $('#reset-btn').on('click', function () {
        $('#filter-form')[0].reset();
        fetchData();
    });

    // Pagination AJAX
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        fetchData($(this).attr('href'));
    });

});
</script>

@endsection
