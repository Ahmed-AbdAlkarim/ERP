@extends('layouts.master')

@section('title', 'الجرد المعلق')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12"> 
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ملفات الجرد المعلقة للاعتماد</h5>
                    <div>
                        <form action="{{ route('admin.inventory.upload.store') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <input 
                                type="file" 
                                name="file" 
                                accept=".xlsx,.csv" 
                                class="d-none" 
                                id="file-upload"
                                onchange="this.form.submit()"
                            >
                            <label for="file-upload" class="btn btn-info me-2">
                                <i class="bx bx-upload me-1"></i>رفع ملف جديد
                            </label>
                        </form>

                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary">
                            <i class="bx bx-arrow-back me-1"></i> العودة للقائمة الرئيسية
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        الملفات المرفوعة بانتظار اعتمادها من قبل المسؤول.
                    </p>

                    @if($batches->count() > 0)
                        <div class="row">
                            @foreach($batches as $batch)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $batch->file_name }}</h6>
                                            <p class="card-text text-muted small">
                                                تم الرفع بواسطة: {{ $batch->creator->name ?? 'غير محدد' }}<br>
                                                تاريخ الرفع: {{ $batch->created_at->format('Y-m-d H:i') }}<br>
                                                عدد التعديلات: {{ $batch->adjustments->count() }}
                                            </p>
                                            <form action="{{ route('admin.inventory.approve', $batch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('هل أنت متأكد من اعتماد هذا الجرد؟')">
                                                    <i class="bx bx-check me-1"></i>اعتماد
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.inventory.reject', $batch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('هل أنت متأكد من رفض هذا الجرد؟')">
                                                    <i class="bx bx-x me-1"></i>رفض
                                                </button>  
                                            </form>
                                            <a href="{{ route('admin.inventory.excel_show',$batch->id) }}"
                                            class="btn btn-sm btn-primary">
                                            عرض التفاصيل
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد ملفات جرد معلقة حالياً.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
