<table class="table table-hover mb-0">
    <thead class="table-light">
        <tr>
            <th class="border-0">الاسم</th>
            <th class="border-0">رقم الهاتف</th>
            <th class="border-0">العنوان</th>
            <th class="border-0">آخر شراء</th>
            <th class="border-0">المديونية</th>
            <th class="border-0">إجراءات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $customer)
        <tr>
            <td class="fw-bold">{{ $customer->name }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->address }}</td>
            <td>{{ $customer->last_purchase_date ? \Carbon\Carbon::parse($customer->last_purchase_date)->format('d/m/Y') : '—' }}</td>
            <td class="fw-bold text-danger">{{ number_format($customer->debt, 2) }} ج.م</td>
            <td>
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-info btn-sm me-1">
                    <i class="fas fa-eye"></i> عرض
                </a>
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm me-1">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('هل تريد الحذف؟')">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="fas fa-users fa-2x mb-2"></i>
                <br> العميل الذي تبحث عنه غير موجود
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@if($customers->hasPages())
<div class="card-footer bg-white border-0">
    {{ $customers->links() }}
</div>
@endif
