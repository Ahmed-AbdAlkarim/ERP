@extends('layouts.master')

@section('title','تعديل الصلاحية')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تعديل الصلاحية: {{ $permission->display_name }}</h1>
            <p class="text-muted mb-0">تعديل بيانات الصلاحية</p>
        </div>
        <div>
            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>عرض التفاصيل
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 text-body">بيانات الصلاحية</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">الاسم للعرض <span class="text-danger">*</span></label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $permission->display_name) }}" class="form-control @error('display_name') is-invalid @enderror" required>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">الاسم للتحقق (Slug) <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-success">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from display name
document.getElementById('display_name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    document.getElementById('name').value = slug;
});
</script>
@endsection
