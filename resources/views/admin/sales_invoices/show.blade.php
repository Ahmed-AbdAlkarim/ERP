@extends('layouts.master')

@section('title', 'عرض فاتورة')

@section('content')
<div class="container mt-4">

    <h2>فاتورة بيع رقم: {{ $invoice->invoice_number }}</h2>

    <p>التاريخ: {{ $invoice->invoice_date->format('Y-m-d H:i') }}</p>

    @if($invoice->customer)
        <p>العميل: {{ $invoice->customer->name }}</p>
        <p>الهاتف: {{ $invoice->customer->phone }}</p>
        <p>العنوان: {{ $invoice->customer->address ?? '-' }}</p>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>الصنف</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3 text-end">

        <p>المجموع:
            <strong>{{ number_format($invoice->subtotal, 2) }} ج.م</strong>
        </p>

        <p>الخصم:
            <strong>{{ number_format($invoice->discount, 2) }} ج.م</strong>
        </p>

        <p>الإجمالي بعد الخصم:
            <strong>{{ number_format($invoice->total, 2) }} ج.م</strong>
        </p>

        <hr>

        <p>المبلغ المدفوع:
            <strong class="text-primary">
                {{ number_format($invoice->paid_amount ?? 0, 2) }} ج.م
            </strong>
        </p>

        <p>المبلغ المتبقي:
            <strong class="text-danger">
                {{ number_format($invoice->remaining_amount ?? 0, 2) }} ج.م
            </strong>
        </p>

        <hr>
        @can('show_sales_invoice_profit')
        <p>الربح:
            <strong>{{ number_format($invoice->profit, 2) }} ج.م</strong>
        </p>
        @endcan

    </div>

    {{-- الازرار جنب بعض --}}
    <div class="d-flex gap-2 mt-3">
        @if(auth()->user()->can('print_sales_invoice'))
        <form action="{{ route('admin.sales-invoices.print', $invoice->id) }}" method="POST" target="_blank">
            @csrf
            <button class="btn btn-success px-4">طباعة</button>
        </form>
        @endif

        <a href="{{ route('admin.sales-invoices.index') }}" class="btn btn-secondary px-4">
            العودة
        </a>
    </div>

</div>
@endsection
