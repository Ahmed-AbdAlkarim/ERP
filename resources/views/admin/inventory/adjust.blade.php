@extends('layouts.master')

@section('title', 'تسوية مخزون')

@section('content')
<div class="container">
    <h4>تسوية مخزون: {{ $product->name }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.inventory.adjust', $product->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">الكمية في النظام</label>
            <input type="number" class="form-control" value="{{ $product->stock }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">الكمية الفعلية</label>
            <input type="number" name="actual_qty" class="form-control" value="{{ old('actual_qty', $product->stock) }}" min="0" required>
            @error('actual_qty') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">سبب التسوية (اختياري)</label>
            <select name="reason" class="form-control">
                <option value="">اختر السبب</option>
                <option value="damaged">هالك (Damaged)</option>
                <option value="count_error">خطأ جرد</option>
                <option value="manual_correction">تصحيح يدوي</option>
                <option value="other">أخرى</option>
            </select>
            <small class="form-text text-muted">لو اخترت "أخرى" اكتب التفاصيل في حقل الملاحظات في الأسفل.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">ملاحظات إضافية</label>
            <textarea name="reason_text" class="form-control" rows="3" placeholder="تفاصيل إضافية"></textarea>
        </div>

        <button type="submit" class="btn btn-success">اعتماد التسوية</button>
        <a href="{{ route('admin.inventory.card', $product->id) }}" class="btn btn-secondary">الغاء</a>
    </form>
</div>
@endsection
