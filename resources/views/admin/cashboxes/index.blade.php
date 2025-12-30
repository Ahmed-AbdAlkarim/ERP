@extends('layouts.master')

@section('title', 'الخزن')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">الخزن</h1>
            <p class="text-muted mb-0">إدارة الخزن والمعاملات المالية</p>
        </div>
        <div>
            <a href="{{ route('admin.cashboxes.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>إضافة خزنة جديدة
            </a>
            <a href="{{ route('admin.cashboxes.transfer.form') }}" class="btn btn-primary ms-2">
                <i class="fas fa-exchange-alt me-2"></i>تحويل أموال
            </a>

            <a href="{{ route('admin.cashboxes.transactions') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-list me-2"></i>عرض جميع العمليات
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $totalBalance = 0;
        foreach ($mainCashboxes as $cashbox) {
            $totalBalance += $cashbox->balance;
        }
        foreach ($dailyCashboxes as $cashbox) {
            $totalBalance += $cashbox->balance;
        }
    @endphp

    <!-- كارت إجمالي المبالغ -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-calculator me-2"></i>إجمالي المبالغ في الخزن
            </h5>
        </div>
        <div class="card-body">
            <h3 class="text-success fw-bold">{{ number_format($totalBalance, 2) }} ج.م</h3>
            <p class="text-muted mb-0">مجموع أرصدة الخزن الرئيسية واليومية</p>
        </div>
    </div>

    <!-- الخزن الرئيسية -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-building me-2"></i>الخزن الرئيسية
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">اسم الخزنة</th>
                            <th class="border-0">الرصيد الحالي</th>
                            <th class="border-0">الحالة</th>
                            <th class="border-0">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mainCashboxes as $cashbox)
                        <tr>
                            <td>{{ $cashbox->id }}</td>
                            <td class="fw-bold">{{ $cashbox->name }}</td>
                            <td class="fw-bold text-success">{{ number_format($cashbox->balance, 2) }} ج.م</td>
                            <td>
                                @if($cashbox->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.cashboxes.show', $cashbox->id) }}" class="btn btn-info btn-sm me-1">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                <a href="{{ route('admin.cashboxes.edit', $cashbox->id) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <form action="{{ route('admin.cashboxes.destroy', $cashbox->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('هل أنت متأكد من حذف هذه الخزنة؟')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>لا توجد خزن رئيسية متاحة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- الخزن اليومية -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-info">
                <i class="fas fa-calendar-day me-2"></i>الخزن اليومية
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">اسم الخزنة</th>
                            <th class="border-0">الرصيد الحالي</th>
                            <th class="border-0">الحالة</th>
                            <th class="border-0">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyCashboxes as $cashbox)
                        <tr>
                            <td>{{ $cashbox->id }}</td>
                            <td class="fw-bold">{{ $cashbox->name }}</td>
                            <td class="fw-bold text-success">{{ number_format($cashbox->balance, 2) }} ج.م</td>
                            <td>
                                @if($cashbox->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.cashboxes.show', $cashbox->id) }}" class="btn btn-info btn-sm me-1">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                <a href="{{ route('admin.cashboxes.edit', $cashbox->id) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <form action="{{ route('admin.cashboxes.destroy', $cashbox->id) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('هل أنت متأكد من حذف هذه الخزنة؟')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>لا توجد خزن يومية متاحة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
