@extends('layouts.master')

@section('title', 'تعديل المصروف')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تعديل المصروف</h1>
            <p class="text-muted mb-0">قم بتعديل بيانات المصروف</p>
        </div>
        <div>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">
                رجوع
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label>العنوان *</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $expense->title) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>الفئة *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">اختر الفئة</option>
                                    <option value="electricity" {{ old('category', $expense->category) == 'electricity' ? 'selected' : '' }}>كهرباء</option>
                                    <option value="rent" {{ old('category', $expense->category) == 'rent' ? 'selected' : '' }}>إيجار</option>
                                    <option value="salaries" {{ old('category', $expense->category) == 'salaries' ? 'selected' : '' }}>مرتبات</option>
                                    <option value="shipping" {{ old('category', $expense->category) == 'shipping' ? 'selected' : '' }}>شحن</option>
                                    <option value="maintenance" {{ old('category', $expense->category) == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                                    <option value="marketing" {{ old('category', $expense->category) == 'marketing' ? 'selected' : '' }}>تسويق</option>
                                    <option value="office" {{ old('category', $expense->category) == 'office' ? 'selected' : '' }}>مكتب</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>المبلغ *</label>
                                <input type="number" step="0.01" name="amount"
                                    class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>الخزنة *</label>
                                <select name="safe_id" class="form-select" required>
                                    <option value="">اختر الخزنة</option>
                                    @foreach($safes as $safe)
                                        <option value="{{ $safe->id }}" {{ old('safe_id', $expense->safe_id) == $safe->id ? 'selected' : '' }}>
                                            @php
                                                $safeNames = [
                                                    'daily_safe' => 'خزنة يومية',
                                                    'main_safe'  => 'خزنة رئيسية',
                                                ];
                                            @endphp

                                            {{ $safeNames[$safe->name] ?? $safe->name }}

                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>تاريخ المصروف *</label>
                                <input type="date" name="expense_date"
                                    class="form-control"
                                    value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>مرفق (اختياري)</label>
                                <input type="file" name="attachment"
                                    class="form-control" accept="image/*">
                                @if($expense->attachment)
                                    <small class="text-muted">المرفق الحالي: <a href="{{ $expense->attachment_url }}" target="_blank">عرض</a></small>
                                @endif
                            </div>

                            <div class="col-12">
                                <label>ملاحظات</label>
                                <textarea name="notes" class="form-control"
                                    rows="3">{{ old('notes', $expense->notes) }}</textarea>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                حفظ التعديلات
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
