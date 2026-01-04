@extends('layouts.master')

@section('content')
<div class="container">
    <h2>تعديل بيانات العميل</h2>

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
        </div>
        <div class="mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
        </div>
        <div class="mb-3">
            <label>العنوان</label>
            <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
        </div>
        <div class="mb-3">
            <label>تاريخ آخر شراء</label>
            <input type="date" name="last_purchase_date" class="form-control" value="{{ $customer->last_purchase_date }}">
        </div>
        <div class="mb-3">
            <label>المديونية</label>
            <input type="number" step="0.01" name="debt" class="form-control" value="{{ $customer->debt }}">
        </div>

        <div class="mb-3">
            <label>الرصيد</label>
            <input type="number" step="0.01" name="balance" class="form-control" value="{{ $customer->balance }}">
        </div>
        
        <div class="mb-3">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control">{{ $customer->notes }}</textarea>
        </div>
        <button class="btn btn-success">تحديث</button>
    </form>
</div>
@endsection
