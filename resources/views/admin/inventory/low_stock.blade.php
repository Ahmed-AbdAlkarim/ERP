@extends('layouts.master')

@section('title', 'المنتجات منخفضة المخزون')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">المنتجات منخفضة المخزون</h5>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary">
                        <i class="bx bx-arrow-back me-1"></i> العودة للقائمة الرئيسية
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المنتج</th>
                                    <th>المخزون</th>
                                    <th>نقطة إعادة الطلب</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $p)
                                    <tr>
                                        <td>{{ $p->id }}</td>
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->stock }}</td>
                                        <td>{{ $p->reorder_level }}</td>
                                        <td>
                                            <a href="{{ route('admin.inventory.card', $p->id) }}" class="btn btn-sm btn-info rounded-pill d-inline-block text-center" style="width: 120px;">
                                                <i class="bx bx-show me-1"></i> عرض الكارت
                                            </a>
                                            <a href="{{ route('admin.inventory.adjust_form', $p->id) }}" class="btn btn-sm btn-warning rounded-pill d-inline-block text-center" style="width: 120px;">
                                                <i class="bx bx-edit me-1"></i> تسوية
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
