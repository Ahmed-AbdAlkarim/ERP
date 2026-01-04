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
            <form id="filter-form"
                  class="row g-3"
                  method="GET"
                  action="{{ route('admin.expenses.index') }}">

                <div class="col-md-3">
                    <label class="form-label">البحث</label>
                    <input type="text"
                           class="form-control"
                           name="search"
                           placeholder="البحث بالعنوان..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">الفئة</label>
                    <select class="form-select" name="category">
                        <option value="">جميع الفئات</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ', $category)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date"
                           class="form-control"
                           name="start_date"
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date"
                           class="form-control"
                           name="end_date"
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="button"
                            id="reset-btn"
                            class="btn btn-outline-secondary w-100">
                        إعادة تعيين
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm">
        <div id="table-container" class="card-body p-0">
            @include('admin.expenses.partials.table')
        </div>
    </div>
</div>

{{-- AJAX --}}
@push('scripts')
<script>
$(function () {

    let timer = null;

    function fetchData(url = "{{ route('admin.expenses.index') }}") {
        $.ajax({
            url: url,
            method: "GET",
            data: $('#filter-form').serialize(),
            success: function (response) {
                $('#table-container').html(response);
            }
        });
    }

    // امنع submit
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
    });

    // بحث لحظي
    $('input[name="search"]').on('input', function () {
        clearTimeout(timer);
        timer = setTimeout(fetchData, 300);
    });

    // فلترة فورية
    $('select[name="category"], input[name="start_date"], input[name="end_date"]')
        .on('change', function () {
            fetchData();
        });

    // Reset
    $('#reset-btn').on('click', function () {
        $('#filter-form')[0].reset();
        fetchData();
    });

    // Pagination
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        fetchData($(this).attr('href'));
    });

});
</script>
@endpush


@endsection
