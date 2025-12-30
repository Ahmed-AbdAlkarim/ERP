@extends('layouts.master')
@section('title', 'تعديل الصنف')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-4 text-primary fw-bold">تعديل الصنف</h4>

    {{-- عرض رسائل الأخطاء --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- اسم الصنف --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم الصنف</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $product->name) }}">
            </div>

            {{-- النوع --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="laptop" {{ old('type', $product->type) == 'laptop' ? 'selected' : '' }}>لابتوب</option>
                    <option value="mobile" {{ old('type', $product->type) == 'mobile' ? 'selected' : '' }}>موبايل</option>
                    <option value="accessory" {{ old('type', $product->type) == 'accessory' ? 'selected' : '' }}>إكسسوار</option>
                    <option value="spare" {{ old('type', $product->type) == 'spare' ? 'selected' : '' }}>قطعة غيار</option>
                    <option value="service" {{ old('type', $product->type) == 'service' ? 'selected' : '' }}>خدمة</option>
                </select>
            </div>

            {{-- الموديل --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الموديل</label>
                <input type="text" name="model" class="form-control"
                       value="{{ old('model', $product->model) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">الحالة</label>
                <select name="condition" class="form-select">
                    <option value="new" {{ old('condition', $product->condition) == 'new' ? 'selected' : '' }}>جديد</option>
                    <option value="used" {{ old('condition', $product->condition) == 'used' ? 'selected' : '' }}>مستعمل</option>
                    <option value="imported" {{ old('condition', $product->condition) == 'imported' ? 'selected' : '' }}>مستورد</option>
                </select>
            </div>


            {{-- SKU --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الباركود / SKU</label>
                <input type="text" name="sku" class="form-control"
                       value="{{ old('sku', $product->sku) }}">
            </div>
        </div>

        <hr>

        <h5 class="text-primary fw-bold mb-3">الأسعار</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">سعر الشراء</label>
                <input type="number" step="0.01" name="purchase_price" class="form-control"
                       value="{{ old('purchase_price', $product->purchase_price) }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">سعر البيع</label>
                <input type="number" step="0.01" name="selling_price" class="form-control"
                       value="{{ old('selling_price', $product->selling_price) }}">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">أقل سعر مسموح</label>
                <input type="number" step="0.01" name="min_allowed_price" class="form-control"
                       value="{{ old('min_allowed_price', $product->min_allowed_price) }}">
            </div>
        </div>

        <hr>

        <h5 class="text-primary fw-bold mb-3">المخزون</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">المخزون</label>
                <input type="number" name="stock" class="form-control"
                       value="{{ old('stock', $product->stock) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">حد إعادة الطلب</label>
                <input type="number" name="reorder_level" class="form-control"
                       value="{{ old('reorder_level', $product->reorder_level) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">هل هو خدمة؟</label>
                <select name="is_service" class="form-select">
                    <option value="0" {{ old('is_service', $product->is_service) == 0 ? 'selected' : '' }}>لا</option>
                    <option value="1" {{ old('is_service', $product->is_service) == 1 ? 'selected' : '' }}>نعم</option>
                </select>
            </div>
        </div>

        <hr>

        <h5 class="text-primary fw-bold mb-3">الضمان</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نوع الضمان</label>
                <input type="text" name="warranty_type" class="form-control"
                       value="{{ old('warranty_type', $product->warranty_type) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">مدة الضمان (أيام)</label>
                <input type="number" name="warranty_period_days" class="form-control"
                       value="{{ old('warranty_period_days', $product->warranty_period_days) }}">
            </div>
        </div>

        <hr>

        <h5 class="text-primary fw-bold mb-3">الصورة</h5>

        @if($product->image)
        <div class="mb-3">
            <label class="form-label d-block">الصورة الحالية</label>
            <img src="{{ asset('storage/' . $product->image) }}" width="120" class="rounded border shadow-sm mb-2">
        </div>
        @endif

        <div class="mb-3">
            <label class="form-label">تغيير الصورة</label>
            <input type="file" name="image" class="form-control">
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $product->notes) }}</textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">تحديث</button>
        </div>
    </form>
</div>
@endsection
