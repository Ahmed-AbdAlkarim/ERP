@extends('layouts.master')

@section('title', 'كارت الصنف')

@section('content')

<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">كارت الصنف</h1>
            <p class="text-muted mb-0">{{ $product->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.inventory.adjust_form', $product->id) }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-balance-scale me-2"></i>تسوية المخزون
            </a>
        </div>
    </div>

    <!-- Product Info Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-boxes fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">المخزون الحالي</h6>
                        <h4 class="card-text mb-0 fw-bold">{{ $product->stock }}</h4>
                        <small class="text-muted">وحدة</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">مستوى التنبيه</h6>
                        <h4 class="card-text mb-0 fw-bold">{{ $product->reorder_level }}</h4>
                        <small class="text-muted">وحدة</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movements Table -->
    

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold text-success mb-0">وارد المنتج (مشتريات)</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>المورد</th>
                        <th>الكمية</th>
                        <th>سعر الشراء</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $i => $item)
                    @can('view_purchase_invoice')
                    <tr
                        style="cursor: pointer"
                        onclick="window.location='{{ route('admin.purchase_invoices.show', $item->invoice->id) }}'"
                    >
                    @endcan

                        <td>{{ $purchases->firstItem() + $i }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->invoice->date)->format('d-m-Y') }}</td>
                        <td>{{ $item->invoice->supplier->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->purchase_price,2) }}</td>
                        <td>{{ number_format($item->total,2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">لا توجد مشتريات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $purchases->links() }}
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold text-primary mb-0">صادر المنتج (مبيعات)</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>العميل</th>
                        <th>الكمية</th>
                        <th>سعر البيع</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $i => $item)
                    @can('view_sales_invoice')
                    <tr
                        style="cursor: pointer"
                        onclick="window.location='{{ route('admin.sales-invoices.show', $item->invoice->id) }}'"
                    >
                    @endcan
                        <td>{{ $sales->firstItem() + $i }}</td>
                        <td>{{ $item->invoice->invoice_date->format('d-m-Y | h:i A') }}</td>
                        <td>{{ $item->invoice->customer->name ?? 'نقدي' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ number_format($item->price,2) }}</td>
                        <td>{{ number_format($item->total,2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">لا توجد مبيعات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $sales->links() }}
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold text-danger mb-0">مرتجعات المنتج</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الكمية</th>
                        <th>سعر المرتجع</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $i => $item)
                    <tr class="clickable-row" style="cursor: pointer"
                        onclick="window.location='{{ route('admin.sales_returns.show', $item->salesReturn->id) }}'">
                        <td>{{ $returns->firstItem() + $i }}</td>
                        <td>{{ $item->salesReturn->return_date->format('d-m-Y | h:i A') }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ number_format($item->return_price,2) }}</td>
                        <td>{{ number_format($item->total,2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            لا توجد مرتجعات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $returns->links() }}
    </div>


    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold text-warning mb-0">تسويات المخزون</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الكمية</th>
                        <th>قبل</th>
                        <th>بعد</th>
                        <th>السبب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $i => $m)
                    <tr>
                        <td>{{ $adjustments->firstItem() + $i }}</td>
                        <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $m->quantity }}</td>
                        <td>{{ $m->before_qty }}</td>
                        <td>{{ $m->after_qty }}</td>
                        <td>
                            @php
                                $reasons = [
                                    'count_error' => 'خطأ جرد',
                                    'damaged' => 'هالك',
                                    'manual_correction' => 'تصحيح يدوي',
                                    'other' => 'أخرى',
                                ];
                            @endphp

                            {{ $reasons[$m->note] ?? $m->note ?? '-' }}
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">لا توجد تسويات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $adjustments->links() }}
    </div>




</div>

@endsection
