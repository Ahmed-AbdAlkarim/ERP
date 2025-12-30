@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">الصيانة</h1>
            <p class="text-muted mb-0">إدارة طلبات الصيانة</p>
        </div>

        <div>
            @can('create_maintenance')
            <a href="{{ route('admin.maintenances.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>إضافة طلب صيانة جديد
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

    <!-- Maintenances Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-body">قائمة طلبات الصيانة</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>العميل</th>
                            <th>نوع الجهاز</th>
                            <th>تاريخ التسليم</th>
                            <th>الحالة</th>
                            <th>تم التحصيل</th>
                            @if(auth()->user()->can('show_maintenance_details') || auth()->user()->can('edit_maintenance') || auth()->user()->can('collect_maintenance') || auth()->user()->can('delete_maintenance'))
                            <th>إجراءات</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenances as $maintenance)
                            <tr>
                                <td>{{ $maintenance->customer->name ?? 'غير محدد' }}</td>
                                <td>{{ $maintenance->device_type }}</td>
                                <td>{{ $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : 'غير محدد' }}</td>
                                <td>
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
                                </td>
                                <td>
                                    @if($maintenance->isCollected())
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-secondary">لا</span>
                                    @endif
                                </td>
                                @if(auth()->user()->can('show_maintenance_details') || auth()->user()->can('edit_maintenance') || auth()->user()->can('collect_maintenance') || auth()->user()->can('delete_maintenance'))
                                <td class="d-flex gap-1">
                                    @can('show_maintenance_details')
                                    <a href="{{ route('admin.maintenances.show', $maintenance->id) }}" class="btn btn-info btn-sm">عرض</a>
                                    @endcan
                                    @can('edit_maintenance')
                                    <a href="{{ route('admin.maintenances.edit', $maintenance->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                    @endcan
                                    @can('collect_maintenance')
                                    @if(!$maintenance->isCollected())
                                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#collectModal{{ $maintenance->id }}">تحصيل</button>
                                    @endif
                                    @endcan
                                    @can('delete_maintenance')
                                    <form action="{{ route('admin.maintenances.destroy', $maintenance->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">حذف</button>
                                    </form>
                                    @endcan
                                </td>
                                @endif
                            </tr>

                            <!-- Collect Modal -->
                            @can('collect_maintenance')
                            <div class="modal fade" id="collectModal{{ $maintenance->id }}" tabindex="-1">
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
                                                    <label for="cashbox_id{{ $maintenance->id }}" class="form-label">الخزنة</label>
                                                    <select name="cashbox_id" id="cashbox_id{{ $maintenance->id }}" class="form-select" required>
                                                        @foreach(\App\Models\Cashbox::where('type', 'daily')->get() as $cashbox)
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
                            @endcan
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-tools fa-2x mb-2"></i>
                                    <br>
                                    لا توجد طلبات صيانة متاحة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($maintenances->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $maintenances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
