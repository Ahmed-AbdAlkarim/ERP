@extends('layouts.master')

@section('title','تفاصيل المرتجع')

@section('content')
<div class="container-fluid my-4">

<h4 class="mb-3">مرتجع رقم {{ $return->return_number }}</h4>

<div class="card mb-3">
    <div class="card-body">
        العميل: {{ $return->customer->name ?? 'نقدي' }} <br>
        التاريخ: {{ $return->return_date->format('d-m-Y H:i') }} <br>
        الإجمالي: {{ number_format($return->total_amount,2) }}
    </div>
</div>

<div class="card">
<table class="table mb-0">
    <thead>
        <tr>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>سعر المرتجع</th>
            <th>الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($return->items as $i)
        <tr>
            <td>{{ $i->product->name }}</td>
            <td>{{ $i->qty }}</td>
            <td>{{ number_format($i->return_price,2) }}</td>
            <td>{{ number_format($i->total,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

</div>
@endsection
