@extends('layouts.master')

@section('title','المرتجعات')

@section('content')
<div class="container-fluid my-4">

    <div class="d-flex justify-content-between mb-4">
        <h4 class="text-primary">مرتجعات البيع</h4>
        <a href="{{ route('admin.sales_returns.create') }}" class="btn btn-primary">
            + إضافة مرتجع
        </a>
    </div>

    <div class="card shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>رقم المرتجع</th>
                    <th>العميل</th>
                    <th>التاريخ</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $i => $r)
                <tr style="cursor:pointer"
                    onclick="location.href='{{ route('admin.sales_returns.show',$r->id) }}'">
                    <td>{{ $returns->firstItem()+$i }}</td>
                    <td>{{ $r->return_number }}</td>
                    <td>{{ $r->customer->name ?? 'نقدي' }}</td>
                    <td>{{ $r->return_date->format('d-m-Y H:i') }}</td>
                    <td>{{ number_format($r->total_amount,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $returns->links() }}
</div>
@endsection
