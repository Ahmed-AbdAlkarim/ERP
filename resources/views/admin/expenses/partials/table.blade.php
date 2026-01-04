<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>العنوان</th>
                <th>الفئة</th>
                <th>المبلغ</th>
                <th>الخزنة</th>
                <th>التاريخ</th>
                <th>المرفق</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td>{{ $expenses->firstItem() + $index }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ ucfirst(str_replace('_',' ', $expense->category)) }}</td>
                    <td>{{ number_format($expense->amount,2) }} ج.م</td>
                    <td>{{ $expense->cashbox?->name ?? 'غير محدد' }}</td>
                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td>
                        @if($expense->attachment)
                            <a href="{{ $expense->attachment_url }}" target="_blank">عرض</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.expenses.edit',$expense) }}"
                           class="btn btn-sm btn-primary">تعديل</a>

                        <form action="{{ route('admin.expenses.destroy',$expense) }}"
                              method="POST"
                              class="d-inline-block"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-danger">
                                حذف
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        class="text-center text-muted py-4">
                        لا توجد مصروفات
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($expenses->hasPages())
<div class="p-3">
    {!! $expenses->appends(request()->query())->links() !!}
</div>
@endif
