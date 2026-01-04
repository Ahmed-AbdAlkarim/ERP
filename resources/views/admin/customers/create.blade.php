@extends('layouts.master')

@section('content')
<div class="container">
    <h2>إضافة عميل جديد</h2>

    <form action="{{ route('admin.customers.store') }}" method="POST">
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
            <label>العنوان</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="mb-3">
            <label>تاريخ آخر شراء</label>
            <input type="date" name="last_purchase_date" class="form-control">
        </div>
        <div class="mb-3">
            <label>المديونية</label>
            <input type="number" step="0.01" name="debt" class="form-control" value="0.00" required>
        </div>

        <div class="mb-3">
            <label>الرصيد</label>
            <input type="number" step="0.01" name="balance" class="form-control" value="0.00" required>
        </div>
        
        <div class="mb-3">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button class="btn btn-success">حفظ</button>
    </form>
</div>
@endsection
