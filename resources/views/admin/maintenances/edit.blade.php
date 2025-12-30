@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تعديل طلب الصيانة</h1>
            <p class="text-muted mb-0">تعديل بيانات طلب الصيانة</p>
        </div>

        <div>
            <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h6 class="mb-0 text-body">بيانات طلب الصيانة</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.maintenances.update', $maintenance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Customer -->
                    <div class="col-md-6 mb-3">
                        <label for="customer_id" class="form-label">العميل <span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">اختر العميل</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $maintenance->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Device Type -->
                    <div class="col-md-6 mb-3">
                        <label for="device_type" class="form-label">نوع الجهاز <span class="text-danger">*</span></label>
                        <input type="text" name="device_type" id="device_type" class="form-control @error('device_type') is-invalid @enderror"
                               value="{{ old('device_type', $maintenance->device_type) }}" placeholder="مثال: هاتف محمول، كمبيوتر محمول" required>
                        @error('device_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fault Type -->
                    <div class="col-md-6 mb-3">
                        <label for="fault_type" class="form-label">نوع العطل <span class="text-danger">*</span></label>
                        <input type="text" name="fault_type" id="fault_type" class="form-control @error('fault_type') is-invalid @enderror"
                               value="{{ old('fault_type', $maintenance->fault_type) }}" placeholder="وصف العطل" required>
                        @error('fault_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cost -->
                    <div class="col-md-6 mb-3">
                        <label for="cost" class="form-label">التكلفة (ج.م) <span class="text-danger">*</span></label>
                        <input type="number" name="cost" id="cost" class="form-control @error('cost') is-invalid @enderror"
                               value="{{ old('cost', $maintenance->cost) }}" step="0.01" min="0" placeholder="0.00" required>
                        @error('cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Delivery Date -->
                    <div class="col-md-6 mb-3">
                        <label for="delivery_date" class="form-label">تاريخ التسليم</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror"
                               value="{{ old('delivery_date', $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : '') }}">
                        @error('delivery_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $maintenance->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="delivered" {{ old('status', $maintenance->status) == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                              rows="4" placeholder="أي ملاحظات إضافية">{{ old('notes', $maintenance->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
