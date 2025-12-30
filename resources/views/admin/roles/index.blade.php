@extends('layouts.master')

@section('title','إدارة الأدوار')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>الأدوار</h4>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">إضافة دور</a>
    </div>

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>Slug</th>
                        <th>الوصف</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->description }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.roles.show',$role->id) }}" class="btn btn-sm btn-info">عرض</a>
                                <a href="{{ route('admin.roles.edit',$role->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                                <form action="{{ route('admin.roles.destroy',$role->id) }}" method="POST" class="d-inline">
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