@extends('layouts.master')

@section('title','تفاصيل ملف الجرد')

@section('content')
<div class="container-xxl container-p-y">
    <div class="card">
        <div class="card-header">
            <h5>تفاصيل الجرد: {{ $batch->file_name }}</h5>
            <p class="text-muted mb-0">
                تم الرفع بواسطة: {{ $batch->creator->name ?? '-' }}
            </p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>المنتج</th>
                            <th>كمية النظام</th>
                            <th>الكمية الفعلية</th>
                            <th>الفرق</th>
                            <th>السبب</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->adjustments as $adj)
                        <tr>
                            <td>{{ $adj->product->sku }}</td>
                            <td>{{ $adj->product->name }}</td>
                            <td>{{ $adj->system_qty }}</td>
                            <td>{{ $adj->actual_qty }}</td>
                            <td class="{{ $adj->difference < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $adj->difference }}
                            </td>
                            <td>{{ $adj->reason }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <form action="{{ route('admin.inventory.reject',$batch->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-danger">رفض الجرد</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
