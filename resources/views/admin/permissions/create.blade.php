@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">إضافة صلاحية جديدة</h1>
            <p class="text-muted mb-0">إنشاء صلاحية جديدة</p>
        </div>
        <div>
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
                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf

                        <div id="permissions-container">
                            <div class="permission-row border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">اسم الصلاحية للتحقق <span class="text-danger">*</span></label>
                                        <input type="text" name="permissions[0][name]" value="{{ old('permissions.0.name') }}" class="form-control permission-name @error('permissions.0.name') is-invalid @enderror" required>
                                        @error('permissions.0.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">اسم الصلاحية للعرض <span class="text-danger">*</span></label>
                                        <input type="text" name="permissions[0][display_name]" value="{{ old('permissions.0.display_name') }}" class="form-control @error('permissions.0.display_name') is-invalid @enderror" required>
                                        @error('permissions.0.display_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-permission" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" id="add-permission" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>إضافة صلاحية أخرى
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-success">حفظ الصلاحيات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let permissionIndex = 1;

    // Function to update remove buttons visibility
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.permission-row');
        const removeButtons = document.querySelectorAll('.remove-permission');
        if (rows.length > 1) {
            removeButtons.forEach(button => button.style.display = 'block');
        } else {
            removeButtons.forEach(button => button.style.display = 'none');
        }
    }

    // Add permission button event
    document.getElementById('add-permission').addEventListener('click', function() {
        const container = document.getElementById('permissions-container');
        const newRow = document.createElement('div');
        newRow.className = 'permission-row border rounded p-3 mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">اسم الصلاحية للتحقق <span class="text-danger">*</span></label>
                    <input type="text" name="permissions[${permissionIndex}][name]" class="form-control permission-name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">اسم الصلاحية للعرض <span class="text-danger">*</span></label>
                    <input type="text" name="permissions[${permissionIndex}][display_name]" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-permission">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        permissionIndex++;
        updateRemoveButtons();
    });

    // Remove permission button event (delegated)
    document.getElementById('permissions-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-permission') || e.target.closest('.remove-permission')) {
            e.target.closest('.permission-row').remove();
            updateRemoveButtons();
        }
    });

    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endsection
