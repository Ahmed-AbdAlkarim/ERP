@extends('layouts.master')

@section('title', 'إضافة خزنة جديدة')

@section('content')
<div class="container mt-4">
    <h2>إضافة خزنة جديدة</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.cashboxes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>اسم الخزنة</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label>نوع الخزنة</label>
            <select name="type" class="form-control" required>
                <option value="main">رئيسية</option>
                <option value="daily">يومية</option>
                <option value="other">أخرى</option>
            </select>
        </div>

        <div class="mb-3">
            <label>الرصيد الابتدائي (اختياري)</label>
            <input type="number" name="balance" class="form-control" step="0.01" min="0" value="{{ old('balance',0) }}">
        </div>

        <button type="submit" class="btn btn-primary">حفظ</button>
        <a href="{{ route('admin.cashboxes.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
