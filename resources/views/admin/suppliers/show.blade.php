@extends('layouts.master')

@section('content')
<div class="container">
    <h2>عرض بيانات المورد</h2>
    <table class="table table-bordered">
        <tr><th>الاسم</th><td>{{ $supplier->name }}</td></tr>
        <tr><th>رقم الهاتف</th><td>{{ $supplier->phone }}</td></tr>
        <tr><th>اسم الشركة</th><td>{{ $supplier->company }}</td></tr>
        @if(auth()->user()->can('show_supplier_debts'))
        <tr><th>المديونية</th><td>{{ $supplier->debt }}</td></tr>
        @endif
        <tr><th>تاريخ آخر توريد</th><td>{{ $supplier->last_supply_date }}</td></tr>
    </table>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-primary">عودة</a>
</div>
@endsection
