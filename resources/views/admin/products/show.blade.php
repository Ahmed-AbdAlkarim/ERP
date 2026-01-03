@extends('layouts.master')
@section('title', 'عرض بيانات الصنف')

@section('content')

<div class="form-card stat-card p-4">
    <h4 class="mb-4 text-primary">عرض بيانات الصنف</h4>

    {{-- صورة المنتج --}}
    @if($product->image)
        <div class="text-center mb-4">
            <img src="{{ asset('storage/'.$product->image) }}" 
                 class="img-fluid rounded shadow"
                 style="max-width: 250px">
        </div>
    @endif

    <div class="row">

        {{-- اسم الصنف --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">اسم الصنف:</label>
            <div class="form-control bg-light">{{ $product->name }}</div>
        </div>

        {{-- النوع --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">النوع:</label>
            <div class="form-control bg-light">
                @switch($product->type)
                    @case('laptop') لابتوب @break
                    @case('mobile') موبايل @break
                    @case('security_camera') كاميرا مراقبة @break
                    @case('photo_camera') كاميرا تصوير @break
                    @case('accessory') إكسسوار @break
                    @case('spare') قطعة غيار @break
                    @case('service') خدمة @break
                @endswitch
            </div>
        </div>

        {{-- الموديل --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">الموديل:</label>
            <div class="form-control bg-light">{{ $product->model ?? '-' }}</div>
        </div>

        {{-- SKU --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">الباركود / SKU:</label>
            <div class="form-control bg-light">{{ $product->sku }}</div>
        </div>

        {{-- الأسعار --}}
        <div class="col-12">
            <hr>
            <h5 class="text-primary">الأسعار</h5>
        </div>

        @can('show_price_product')
        <div class="col-md-4 mb-3">
            <label class="fw-bold">سعر الشراء:</label>
            <div class="form-control bg-light">{{ $product->purchase_price }}</div>
        </div>
        @endcan

        <div class="col-md-4 mb-3">
            <label class="fw-bold">سعر البيع:</label>
            <div class="form-control bg-light">{{ $product->selling_price }}</div>
        </div>

        <div class="col-md-4 mb-3">
            <label class="fw-bold">أقل سعر مسموح:</label>
            <div class="form-control bg-light">{{ $product->min_allowed_price ?? '-' }}</div>
        </div>

        {{-- المخزون --}}
        <div class="col-12">
            <hr>
            <h5 class="text-primary">المخزون</h5>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">المخزون الحالي:</label>
            <div class="form-control bg-light">{{ $product->stock }}</div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">حد إعادة الطلب:</label>
            <div class="form-control bg-light">{{ $product->reorder_level }}</div>
        </div>

        {{-- الضمان --}}
        <div class="col-12">
            <hr>
            <h5 class="text-primary">الضمان</h5>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">نوع الضمان:</label>
            <div class="form-control bg-light">{{ $product->warranty_type ?? '-' }}</div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="fw-bold">مدة الضمان (أيام):</label>
            <div class="form-control bg-light">{{ $product->warranty_period_days ?? '-' }}</div>
        </div>

        {{-- الحالة --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">الحالة:</label>
            <div class="form-control bg-light">
                @switch($product->condition)
                    @case('new') جديد @break
                    @case('used') مستعمل @break
                    @case('imported') مستورد @break
                @endswitch
            </div>
        </div>

        {{-- هل هو خدمة --}}
        <div class="col-md-6 mb-3">
            <label class="fw-bold">هل هو خدمة؟</label>
            <div class="form-control bg-light">
                {{ $product->is_service ? 'نعم' : 'لا' }}
            </div>
        </div>

        {{-- ملاحظات --}}
        <div class="col-12 mb-3">
            <label class="fw-bold">ملاحظات:</label>
            <textarea class="form-control bg-light" rows="3" disabled>
                {{ $product->notes ?? '-' }}
            </textarea>
        </div>

    </div>

    <div class="text-end mt-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4">رجوع</a>
        @can('edit_product')
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary px-4">تعديل</a>
        @endcan
    </div>
</div>

@endsection
