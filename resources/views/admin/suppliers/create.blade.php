@extends('layouts.master')

@section('content')
<div class="container">
    <h2>إضافة مورد جديد</h2>

    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>اسم الشركة</label>
            <input type="text" name="company" class="form-control">
        </div>
        <div class="mb-3">
            <label>المديونية</label>
            <input type="number" step="0.01" name="debt" class="form-control">
        </div>
        <div class="mb-3">
            <label>تاريخ آخر توريد</label>
            <input type="date" name="last_supply_date" class="form-control">
        </div>
        <button class="btn btn-success">حفظ</button>
    </form>
</div>
@endsection
