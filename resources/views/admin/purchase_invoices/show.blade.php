@extends('layouts.master')

@section('title', 'تفاصيل الفاتورة')

@section('content')
<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">تفاصيل فاتورة شراء رقم: {{ $invoice->invoice_number }}</h4>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <h5>بيانات الفاتورة</h5>
                <hr>

                <p><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>المورد:</strong> {{ $invoice->supplier->name }}</p>
                <p><strong>التاريخ:</strong> {{ $invoice->date }}</p>
                <p><strong>المصاريف الإضافية:</strong> {{ number_format($invoice->additional_expenses, 2) }} ج.م</p>
                <p><strong>الإجمالي النهائي:</strong> {{ number_format($invoice->total_cost, 2) }} ج.م</p>
                <p><strong>حالة الدفع:</strong>
                    @if($invoice->payment_status == 'paid') مدفوع
                    @elseif($invoice->payment_status == 'due') أجل
                    @elseif($invoice->payment_status == 'partial') دفع جزئي
                    @endif
                </p>
                @if($invoice->payment_status != 'paid')
                    <p><strong>المبلغ المدفوع:</strong> {{ number_format($invoice->paid_amount, 2) }} ج.م</p>
                    <p><strong>المتبقي:</strong> {{ number_format($invoice->due_amount, 2) }} ج.م</p>
                @endif

                <p><strong>ملاحظات:</strong> {{ $invoice->note ?? 'لا توجد ملاحظات' }}</p>
            </div>

            <h5>الأصناف المشتراة</h5>
            <hr>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>الصنف</th>
                        <th width="120">الكمية</th>
                        <th width="150">سعر الشراء</th>
                        <th width="150">الإجمالي</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->purchase_price, 2) }} ج.م</td>
                            <td>{{ number_format($item->total, 2) }} ج.م</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <a href="{{ route('admin.purchase_invoices.index') }}" class="btn btn-secondary">رجوع</a>
            </div>

        </div>
    </div>

</div>
@endsection
