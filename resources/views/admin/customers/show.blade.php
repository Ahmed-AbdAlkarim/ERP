@extends('layouts.master')

@section('content')
<div class="container">
    <h2>عرض بيانات العميل</h2>
    <table class="table table-bordered">
        <tr><th>الاسم</th><td>{{ $customer->name }}</td></tr>
        <tr><th>رقم الهاتف</th><td>{{ $customer->phone }}</td></tr>
        <tr><th>العنوان</th><td>{{ $customer->address }}</td></tr>
        <tr><th>تاريخ آخر شراء</th><td>{{ $customer->last_purchase_date }}</td></tr>
        @if(auth()->user()->can('show_customer_debts'))
        <tr><th>المديونية</th><td>{{ $customer->debt }}</td></tr>
        @endif
        <tr><th>ملاحظات</th><td>{{ $customer->notes }}</td></tr>
    </table>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-primary">عودة</a>
</div>
@endsection
