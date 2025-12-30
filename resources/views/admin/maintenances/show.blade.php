@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">تفاصيل الصيانة</h1>
            <p class="text-muted mb-0">عرض تفاصيل طلب الصيانة</p>
        </div>

        <div>
            <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
            </a>
            @can('edit_maintenance')
            <a href="{{ route('admin.maintenances.edit', $maintenance->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
            @endcan
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

    <!-- Maintenance Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 text-body">معلومات الصيانة</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">العميل:</dt>
                        <dd class="col-sm-9">{{ $maintenance->customer->name ?? 'غير محدد' }}</dd>

                        <dt class="col-sm-3">نوع الجهاز:</dt>
                        <dd class="col-sm-9">{{ $maintenance->device_type }}</dd>

                        <dt class="col-sm-3">نوع العطل:</dt>
                        <dd class="col-sm-9">{{ $maintenance->fault_type }}</dd>

                        <dt class="col-sm-3">التكلفة:</dt>
                        <dd class="col-sm-9">{{ number_format($maintenance->cost, 2) }} ج.م</dd>

                        <dt class="col-sm-3">تاريخ التسليم:</dt>
                        <dd class="col-sm-9">{{ $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : 'غير محدد' }}</dd>

                        <dt class="col-sm-3">الحالة:</dt>
                        <dd class="col-sm-9">
                            @switch($maintenance->status)
                                @case('pending')
                                    <span class="badge bg-warning">في الانتظار</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info">قيد التنفيذ</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">مكتمل</span>
                                    @break
                                @case('delivered')
                                    <span class="badge bg-primary">تم التسليم</span>
                                    @break
                            @endswitch
                        </dd>

                        <dt class="col-sm-3">تم التحصيل:</dt>
                        <dd class="col-sm-9">
                            @if($maintenance->isCollected())
                                <span class="badge bg-success">نعم</span>
                            @else
                                <span class="badge bg-secondary">لا</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">الملاحظات:</dt>
                        <dd class="col-sm-9">{{ $maintenance->notes ?: 'لا توجد ملاحظات' }}</dd>

                        <dt class="col-sm-3">تاريخ الإنشاء:</dt>
                        <dd class="col-sm-9">{{ $maintenance->created_at->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-3">آخر تحديث:</dt>
                        <dd class="col-sm-9">{{ $maintenance->updated_at->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 text-body">الإجراءات</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('edit_maintenance')
                        <a href="{{ route('admin.maintenances.edit', $maintenance->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>تعديل الصيانة
                        </a>
                        @endcan

                        @can('collect_maintenance')
                        @if(!$maintenance->isCollected())
                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#collectModal">
                            <i class="fas fa-money-bill-wave me-2"></i>تحصيل المبلغ
                        </button>
                        @endif
                        @endcan

                        @can('delete_maintenance')
                        <form action="{{ route('admin.maintenances.destroy', $maintenance->id) }}" method="POST" class="d-inline-block w-100">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger w-100" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                <i class="fas fa-trash me-2"></i>حذف الصيانة
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Status History or Additional Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 text-body">معلومات إضافية</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>رقم الصيانة:</strong> #{{ $maintenance->id }}</p>
                    <p class="mb-1"><strong>تم الإنشاء بواسطة:</strong> {{ $maintenance->createdBy->name ?? 'غير محدد' }}</p>
                    <p class="mb-1"><strong>آخر تحديث بواسطة:</strong> {{ $maintenance->updatedBy->name ?? 'غير محدد' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Collect Modal -->
    @can('collect_maintenance')
    @if(!$maintenance->isCollected())
    <div class="modal fade" id="collectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تحصيل المبلغ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.maintenances.collect', $maintenance->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>تحصيل مبلغ {{ number_format($maintenance->cost, 2) }} ج.م من العميل {{ $maintenance->customer->name ?? 'غير محدد' }}</p>
                        <div class="mb-3">
                            <label for="cashbox_id" class="form-label">الخزنة</label>
                            <select name="cashbox_id" id="cashbox_id" class="form-select" required>
                                @foreach(\App\Models\Cashbox::all() as $cashbox)
                                    <option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تحصيل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan
</div>
@endsection
