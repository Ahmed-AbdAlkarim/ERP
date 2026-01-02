@extends('layouts.master')

@section('title', 'تفاصيل الخزنة: ' . $cashbox->name)

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">{{ $cashbox->name }}</h1>
            <p class="text-muted mb-0">تفاصيل الخزنة والمعاملات</p>
        </div>
        <div>
            <a href="{{ route('admin.cashboxes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للخزن
            </a>
            <a href="{{ route('admin.cashboxes.transfer.form') }}" class="btn btn-primary">
                <i class="fas fa-exchange-alt me-2"></i>تحويل أموال
            </a>
        </div>
    </div>

    <!-- Cashbox Info -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">الرصيد الحالي</h5>
                    <h2 class="mb-0">{{ number_format($cashbox->balance, 2) }} ج.م</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">إجمالي الوارد</h5>
                    <h4 class="mb-0">{{ number_format($cashbox->transactions()->where('type', 'in')->sum('amount'), 2) }} ج.م</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">إجمالي الصادر</h5>
                    <h4 class="mb-0">{{ number_format($cashbox->transactions()->where('type', 'out')->sum('amount'), 2) }} ج.م</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">المعاملات</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">التاريخ</th>
                            <th class="border-0">النوع</th>
                            <th class="border-0">المبلغ</th>
                            <th class="border-0">المستخدم</th>
                            <th class="border-0">الوحدة</th>
                            <th class="border-0">ملاحظة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $transactions->firstItem() + $index }}</td>
                            <td>{{ $transaction->formatted_date }}</td>         
                            <td>
                                @if($transaction->type == 'in')
                                    <span class="badge bg-success">وارد</span>
                                @else
                                    <span class="badge bg-danger">صادر</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($transaction->amount, 2) }} ج.م</td>
                            <td>{{ $transaction->user->name ?? '—'}}</td>
                            <td>
                                <span class="badge
                                    @if($transaction->module == 'sales_invoice') bg-success
                                    @elseif($transaction->module == 'purchase_invoice') bg-danger
                                    @elseif($transaction->module == 'transfer') bg-warning
                                    @elseif($transaction->module == 'expense') bg-info
                                    @elseif($transaction->module == 'sales_return') bg-primary
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ $transaction->module_label }}
                                </span>
                            </td>
                            <td>{{ $transaction->note }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2"></i>
                                <br>لا توجد معاملات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
