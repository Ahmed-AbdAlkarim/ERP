@extends('layouts.master')

@section('title', 'المنتجات المسوية')

@section('content')

<div class="container-fluid my-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">المنتجات المسوية</h1>
            <p class="text-muted mb-0">قائمة المنتجات التي تمت تسوية مخزونها</p>
        </div>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>العودة للمخزون
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 fw-bold">#</th>
                            <th class="border-0 fw-bold">المنتج</th>
                            <th class="border-0 fw-bold">المخزون الحالي</th>
                            <th class="border-0 fw-bold">آخر تسوية</th>
                            <th class="border-0 fw-bold">تاريخ آخر تسوية</th>
                            <th class="border-0 fw-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        <tr class="border-bottom border-light">
                            <td class="fw-bold text-primary">{{ $products->firstItem() + $index }}</td>
                            <td class="fw-bold">{{ $product->name }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @if($product->stockMovements->isNotEmpty())
                                    {{ $product->stockMovements->first()->quantity }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($product->stockMovements->isNotEmpty())
                                    {{ $product->stockMovements->first()->created_at->format('Y-m-d H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.inventory.card', $product->id) }}" class="btn btn-sm btn-info rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i>عرض الكارت
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-boxes fa-2x mb-2"></i>
                                <br>لا توجد منتجات مسوية
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>

@endsection
