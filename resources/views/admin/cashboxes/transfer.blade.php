@extends('layouts.master')

@section('title', 'تحويل أموال بين الخزن')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تحويل أموال بين الخزن</h1>
            <p class="text-muted mb-0">نقل الأموال من خزنة إلى أخرى</p>
        </div>
        <div>
            <a href="{{ route('admin.cashboxes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للخزن
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">نموذج التحويل</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cashboxes.transfer') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="from_cashbox" class="form-label">من الخزنة</label>
                                <select name="from_cashbox" id="from_cashbox" class="form-select" required>
                                    <option value="">اختر الخزنة المصدر</option>
                                    @foreach($cashboxes as $cashbox)
                                        <option value="{{ $cashbox->id }}" {{ old('from_cashbox') == $cashbox->id ? 'selected' : '' }}>
                                            {{ $cashbox->name }} ({{ number_format($cashbox->balance, 2) }} ج.م)
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_cashbox')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="to_cashbox" class="form-label">إلى الخزنة</label>
                                <select name="to_cashbox" id="to_cashbox" class="form-select" required>
                                    <option value="">اختر الخزنة المستهدفة</option>
                                    @foreach($cashboxes as $cashbox)
                                        <option value="{{ $cashbox->id }}" {{ old('to_cashbox') == $cashbox->id ? 'selected' : '' }}>
                                            {{ $cashbox->name }} ({{ number_format($cashbox->balance, 2) }} ج.م)
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_cashbox')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">المبلغ</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">ملاحظة (اختياري)</label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="أضف ملاحظة للتحويل">{{ old('note') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-exchange-alt me-2"></i>تحويل المبلغ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
