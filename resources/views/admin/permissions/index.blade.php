@extends('layouts.master')

@section('title','إدارة الصلاحيات')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>الصلاحيات</h4>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">إضافة صلاحية</a>
    </div>

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم للتحقق</th>
                        <th>الاسم للعرض</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->display_name }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.permissions.show',$permission->id) }}" class="btn btn-sm btn-info">عرض</a>
                                <a href="{{ route('admin.permissions.edit',$permission->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                                <form action="{{ route('admin.permissions.destroy',$permission->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
