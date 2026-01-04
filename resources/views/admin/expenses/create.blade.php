@extends('layouts.master')

@section('title', 'إضافة مصروف جديد')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">إضافة مصروف جديد</h1>
            <p class="text-muted mb-0">أدخل بيانات المصروف الجديد</p>
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

                    <form method="POST" action="{{ route('admin.expenses.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label>العنوان *</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>الفئة *</label>
                                <select name="category" id="categorySelect" class="form-select" required>
                                    <option value="">اختر الفئة</option>

                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">
                                            {{ ucfirst(str_replace('_',' ', $category)) }}
                                        </option>
                                    @endforeach

                                    <option value="add_new" class="text-primary">
                                        إضافة فئة مصروف
                                    </option>
                                </select>
                            </div>



                            <div class="col-md-6">
                                <label>المبلغ *</label>
                                <input type="number" step="0.01" name="amount"
                                    class="form-control" value="{{ old('amount') }}" required>

                                @error('safe_balance')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label>الخزنة *</label>
                                <select name="cashbox_id" class="form-select" required>
                                    <option value="">اختر الخزنة</option>
                                    @foreach($safes as $safe)
                                        <option value="{{ $safe->id }}">
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
                                    value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>مرفق (اختياري)</labe l>
                                <input type="file" name="attachment"
                                    class="form-control" accept="image/*">
                            </div>

                            <div class="col-12">
                                <label>ملاحظات</label>
                                <textarea name="notes" class="form-control"
                                    rows="3">{{ old('notes') }}</textarea>
                            </div>

                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                حفظ المصروف
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">إضافة فئة مصروف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label>اسم الفئة</label>
                <input type="text" id="newCategoryName" class="form-control"
                    placeholder="مثال: إنترنت">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    إلغاء
                </button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    حفظ
                </button>
            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const categorySelect = document.getElementById('categorySelect');
    const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    const saveBtn = document.getElementById('saveCategoryBtn');

    categorySelect.addEventListener('change', function () {
        if (this.value === 'add_new') {
            this.value = '';
            modal.show();
        }
    });

    saveBtn.addEventListener('click', function () {
        const newCategory = document.getElementById('newCategoryName').value.trim();

        if (!newCategory) {
            alert('اكتب اسم الفئة');
            return;
        }

        // تحويل الاسم لقيمة مناسبة (snake_case)
        const value = newCategory
            .toLowerCase()
            .replace(/\s+/g, '_');

        // إضافة option جديد
        const option = document.createElement('option');
        option.value = value;
        option.textContent = newCategory;
        option.selected = true;

        // إضافته قبل "إضافة فئة مصروف"
        categorySelect.insertBefore(option, categorySelect.lastElementChild);

        // تفريغ input وقفل المودال
        document.getElementById('newCategoryName').value = '';
        modal.hide();
    });

});
</script>

@endsection
