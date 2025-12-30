@extends('layouts.master')

@section('title', 'جميع العمليات على الخزن')

@section('content')
<div class="container-fluid my-4">
    <h1 class="h3 text-primary mb-4">جميع العمليات على الخزن</h1>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الخزنة</th>
                            <th>نوع العملية</th>
                            <th>المبلغ</th>
                            <th>التاريخ</th>
                            <th>الملاحظة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tran)
                        <tr>
                            <td>{{ $tran->id }}</td>
                            <td>{{ $tran->cashbox->name ?? '-' }}</td>
                            <td>
                                @if($tran->type == 'in')
                                    <span class="badge bg-success">إضافة</span>
                                @else
                                    <span class="badge bg-danger">سحب</span>
                                @endif
                            </td>
                            <td>{{ number_format($tran->amount, 2) }} ج.م</td>
                            <td>{{ $tran->date }}</td>
                            <td>{{ $tran->note ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                لا توجد عمليات حتى الآن
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
