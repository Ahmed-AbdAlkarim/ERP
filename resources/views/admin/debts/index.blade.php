@extends('layouts.master')

@section('content')
<div class="container-fluid my-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-primary"><i class="fas fa-credit-card me-2"></i>المديونيات</h1>
            <p class="text-muted mb-0">إدارة مديونيات الموردين والعملاء</p>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $totalSupplierDebts = 0;
        foreach($supplierDebts as $supplierId => $debts) {
            $supplier = $debts->first()->supplier;
            $totalSupplierDebts += $supplier->debt;
        }

        $totalCustomerDebts = 0;
        foreach($customerDebts as $customerId => $invoices) {
            $totalCustomerDebts += $invoices->sum('remaining_amount');
        }
    @endphp

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">إجمالي مديونيات الموردين</h6>
                    <h4 class="text-danger mb-0">{{ number_format($totalSupplierDebts, 2) }} ج.م</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">إجمالي مديونيات العملاء</h6>
                    <h4 class="text-success mb-0">{{ number_format($totalCustomerDebts, 2) }} ج.م</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- مديونيات الموردين -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header">
            <h6 class="mb-0 text-body">مديونيات الموردين</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>المورد</th>
                            <th>إجمالي المديونية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierDebts as $supplierId => $debts)
                            @php
                                $supplier = $debts->first()->supplier;
                                $totalDebt = $supplier->debt;
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $supplier->name }}</td>
                                <td class="fw-bold text-danger">{{ number_format($totalDebt, 2) }} ج.م</td>
                                <td>
                                    <button onclick="openSupplierPaymentModal({{ $supplier->id }}, '{{ $supplier->name }}', {{ $totalDebt }})"
                                            class="btn btn-primary btn-sm">
                                        <i class="fas fa-money-bill-wave me-1"></i>دفع دفعة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <br>لا توجد مديونيات للموردين
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- مديونيات العملاء -->
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h6 class="mb-0 text-body">مديونيات العملاء</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>العميل</th>
                            <th>إجمالي المديونية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerDebts as $customerId => $invoices)
                            @php
                                $customer = $invoices->first()->customer;
                                $totalDebt = $invoices->sum('remaining_amount');
                            @endphp
                            <tr>
                                <td class="fw-bold">{{ $customer->name }}</td>
                                <td class="fw-bold text-success">{{ number_format($totalDebt, 2) }} ج.م</td>
                                <td>
                                    <button onclick="openCustomerPaymentModal({{ $customer->id }}, '{{ $customer->name }}', {{ $totalDebt }})"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-hand-holding-usd me-1"></i>استلام دفعة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <br>لا توجد مديونيات للعملاء
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal لدفع مديونية المورد -->
<div class="modal fade" id="supplierPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">دفع دفعة للمورد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.debts.pay_supplier') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="supplier_id">
                    <div class="mb-3">
                        <label class="form-label">المورد:</label>
                        <p class="form-control-plaintext fw-bold" id="supplier_name"></p>
                    </div>
                    <div class="mb-3">
                        <label for="cashbox_id" class="form-label">الخزنة:</label>
                        <select name="cashbox_id" class="form-select" required>
                            @foreach($cashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}">{{ $cashbox->name }} (رصيد: {{ number_format($cashbox->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ:</label>
                        <input type="number" step="0.01" name="amount" id="supplier_amount" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">دفع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal لاستلام دفعة من العميل -->
<div class="modal fade" id="customerPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">استلام دفعة من العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.debts.receive_customer') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="mb-3">
                        <label class="form-label">العميل:</label>
                        <p class="form-control-plaintext fw-bold" id="customer_name"></p>
                    </div>
                    <div class="mb-3">
                        <label for="cashbox_id" class="form-label">الخزنة اليومية:</label>
                        <select name="cashbox_id" class="form-select" required>
                            @foreach($dailyCashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}">{{ $cashbox->name }} (رصيد: {{ number_format($cashbox->balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ:</label>
                        <input type="number" step="0.01" name="amount" id="customer_amount" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">استلام</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSupplierPaymentModal(supplierId, supplierName, maxAmount) {
    document.getElementById('supplier_id').value = supplierId;
    document.getElementById('supplier_name').textContent = supplierName;
    document.getElementById('supplier_amount').max = maxAmount;
    new bootstrap.Modal(document.getElementById('supplierPaymentModal')).show();
}

function openCustomerPaymentModal(customerId, customerName, maxAmount) {
    document.getElementById('customer_id').value = customerId;
    document.getElementById('customer_name').textContent = customerName;
    new bootstrap.Modal(document.getElementById('customerPaymentModal')).show();
}
</script>
@endsection