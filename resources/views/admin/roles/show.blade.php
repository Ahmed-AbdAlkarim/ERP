@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary">عرض الدور: {{ $role->name }}</h1>
            <p class="text-muted mb-0">{{ $role->description ?? 'لا يوجد وصف' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Role Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 text-body">معلومات الدور</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الدور</label>
                                <p class="mb-0">{{ $role->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">المعرف</label>
                                <p class="mb-0">{{ $role->slug }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الوصف</label>
                        <p class="mb-0">{{ $role->description ?? 'لا يوجد وصف' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تاريخ الإنشاء</label>
                        <p class="mb-0">{{ $role->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Users with this role -->
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 text-body">المستخدمون المرتبطون بهذا الدور ({{ $role->users->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>تاريخ الانضمام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <br>
                            لا يوجد مستخدمون مرتبطون بهذا الدور
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 text-body">الصلاحيات ({{ $role->permissions->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($role->permissions as $permission)
                                <span class="badge bg-primary">{{ $permission->display_name }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <br>
                            لا توجد صلاحيات مرتبطة بهذا الدور
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
