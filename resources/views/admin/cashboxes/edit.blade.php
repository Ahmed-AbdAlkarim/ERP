@extends('layouts.master')

@section('title', 'تعديل الخزنة')

@section('content')
<div class="container mt-4">
    <h2>تعديل الخزنة</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.cashboxes.update', $cashbox->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>اسم الخزنة</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$cashbox->name) }}" required>
        </div>

        <div class="mb-3">
            <label>نوع الخزنة</label>
            <select name="type" class="form-control" required>
                <option value="main" @if($cashbox->type=='main') selected @endif>رئيسية</option>
                <option value="daily" @if($cashbox->type=='daily') selected @endif>يومية</option>
                <option value="other" @if($cashbox->type=='other') selected @endif>أخرى</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" class="form-check-input" value="1" @if($cashbox->is_active) checked @endif>
            <label class="form-check-label">نشط</label>
        </div>

        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('admin.cashboxes.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
