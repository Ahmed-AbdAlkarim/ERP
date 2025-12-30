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
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-bold text-primary">حركات المخزون</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-bold">#</th>
                            <th class="border-0 fw-bold">التاريخ</th>
                            <th class="border-0 fw-bold">النوع</th>
                            <th class="border-0 fw-bold">الكمية</th>
                            <th class="border-0 fw-bold">قبل</th>
                            <th class="border-0 fw-bold">بعد</th>
                            <th class="border-0 fw-bold">سبب التسوية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $index => $m)
                        <tr class="border-bottom border-light">
                            <td class="fw-bold text-primary">{{ $movements->firstItem() + $index }}</td>
                            <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($m->type == 'in')
                                    <span class="badge bg-success rounded-pill px-2 py-1">دخول</span>
                                @elseif($m->type == 'out')
                                    <span class="badge bg-danger rounded-pill px-2 py-1">خروج</span>
                                @elseif($m->type == 'adjustment')
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">تسوية</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-2 py-1">{{ $m->type }}</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $m->quantity }}</td>
                            <td>{{ $m->before_qty }}</td>
                            <td>{{ $m->after_qty }}</td>
                            <td dir="rtl">
                                @if($m->type == 'adjustment')
                                    @if($m->note == 'damaged')
                                        هالك 
                                    @elseif($m->note == 'count_error')
                                        خطأ جرد
                                    @elseif($m->note == 'manual_correction')
                                        تصحيح يدوي
                                    @elseif($m->note == 'other')
                                        أخرى
                                    @else
                                        {{ $m->note ?? '-' }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <br>لا توجد حركات مخزون
                            </td>
                        </tr>w
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($movements->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

</div>

@endsection
