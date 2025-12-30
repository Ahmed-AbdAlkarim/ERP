@extends('layouts.master')

@section('content')
<div class="container">
    <h2>تعديل بيانات المورد</h2>

    <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
        </div>
        <div class="mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}" required>
        </div>
        <div class="mb-3">
            <label>اسم الشركة</label>
            <input type="text" name="company" class="form-control" value="{{ $supplier->company }}">
        </div>
        <div class="mb-3">
            <label>المديونية</label>
            <input type="number" step="0.01" name="debt" class="form-control" value="{{ $supplier->debt }}">
        </div>
        <div class="mb-3">
            <label>تاريخ آخر توريد</label>
            <input type="date" name="last_supply_date" class="form-control" value="{{ $supplier->last_supply_date }}">
        </div>
        <button class="btn btn-success">تحديث</button>
    </form>
</div>
@endsection
