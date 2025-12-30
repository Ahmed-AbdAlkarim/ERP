@extends('layouts.master')

@section('title', 'عرض المستخدم')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">تفاصيل المستخدم: {{ $user->name }}</h5>
                    <div>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">تعديل</a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">العودة إلى القائمة</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID:</label>
                                <p>{{ $user->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الاسم:</label>
                                <p>{{ $user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">البريد الإلكتروني:</label>
                                <p>{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الإنشاء:</label>
                                <p>{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ التحديث الأخير:</label>
                                <p>{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الأدوار:</label>
                                @if($user->roles->count() > 0)
                                    <div class="d-flex flex-wrap">
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary me-2 mb-2">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <p>لا توجد أدوار</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Permissions by Role Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الصلاحيات المرتبطة بالأدوار:</label>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <div class="mb-3">
                                            <strong>{{ $role->name }}:</strong>
                                            @if($role->permissions->count() > 0)
                                                <div class="d-flex flex-wrap mt-2">
                                                    @foreach($role->permissions as $permission)
                                                        <span class="badge bg-success me-2 mb-2">{{ $permission->display_name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="mt-2">لا توجد صلاحيات لهذا الدور</p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p>لا توجد أدوار</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
