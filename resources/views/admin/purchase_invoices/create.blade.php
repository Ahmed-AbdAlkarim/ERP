@extends('layouts.master')

@section('title', 'إنشاء فاتورة شراء')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">إنشاء فاتورة شراء جديدة</h4>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.purchase_invoices.store') }}" method="POST">
                @csrf

                {{-- بيانات الفاتورة --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label>رقم الفاتورة</label>
                        <input type="text" class="form-control" value="{{ $invoice_number }}" readonly>
                        <input type="hidden" name="invoice_number" value="{{ $invoice_number }}">
                    </div>

                    <div class="col-md-3">
                        <label>المورد</label>
                        <select name="supplier_id" class="form-control" required>
                            <option disabled selected>اختر المورد</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>تاريخ الفاتورة</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>طريقة الدفع</label>
                        <select name="payment_status" id="payment_status" class="form-control" required>
                            <option value="paid">كاش</option>
                            <option value="partial">دفع جزئي</option>
                            <option value="due" selected>أجل</option>
                        </select>
                    </div>
                </div>

                {{-- الدفع --}}
                <div id="cashbox_row" style="display:none;">
                    <h6>تفاصيل الدفع</h6>

                    <div id="payment_details"></div>

                    <button type="button" id="add_payment" class="btn btn-secondary mb-2">
                        + إضافة دفعة
                    </button>

                    <div class="mb-3">
                        <strong>إجمالي المدفوع: </strong>
                        <span id="total_paid">0</span> ج.م
                    </div>
                </div>

                <hr>

                {{-- الأصناف --}}
                <h5>الأصناف المشتراة</h5>

                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th>الكمية</th>
                            <th>سعر الشراء</th>
                            <th>الإجمالي</th>
                            <th>حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-control" required>
                                    <option disabled selected>اختر الصنف</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1">
                            </td>
                            <td>
                                <input type="number" name="items[0][purchase_price]" class="form-control price" value="0" step="0.01">
                            </td>
                            <td>
                                <input type="number" class="form-control total" readonly value="0">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" id="addRow" class="btn btn-secondary mb-3">
                    + إضافة صنف
                </button>

                <hr>

                <div class="mb-3">
                    <label>مصاريف إضافية</label>
                    <input type="number" name="additional_expenses" class="form-control additional" value="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label>الإجمالي النهائي</label>
                    <input type="number" id="finalTotal" class="form-control" readonly>
                    <input type="hidden" name="total_cost" id="total_cost">
                </div>

                <button type="submit" class="btn btn-primary">
                    حفظ الفاتورة
                </button>

            </form>
        </div>
    </div>
</div>

{{-- ================= JS ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================= الأصناف ================= */
    let rowIndex = 1;

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            let qty = parseFloat(row.querySelector('.quantity').value) || 0;
            let price = parseFloat(row.querySelector('.price').value) || 0;
            let rowTotal = qty * price;
            row.querySelector('.total').value = rowTotal.toFixed(2);
            total += rowTotal;
        });

        let additional = parseFloat(document.querySelector('.additional').value) || 0;
        total += additional;

        document.getElementById('finalTotal').value = total.toFixed(2);
        document.getElementById('total_cost').value = total.toFixed(2);
    }

    document.getElementById('addRow').onclick = function () {
        let tbody = document.querySelector('#itemsTable tbody');
        let tr = document.createElement('tr');

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-control" required>
                    <option disabled selected>اختر الصنف</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity" value="1"></td>
            <td><input type="number" name="items[${rowIndex}][purchase_price]" class="form-control price" value="0"></td>
            <td><input type="number" class="form-control total" readonly value="0"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        calculateTotal();
    };

    document.addEventListener('input', e => {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('price') || e.target.classList.contains('additional')) {
            calculateTotal();
        }
    });

    document.addEventListener('click', e => {
        if (e.target.classList.contains('removeRow')) {
            let rows = document.querySelectorAll('#itemsTable tbody tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        }
    });

    /* ================= الدفع ================= */
    const paymentStatus = document.getElementById('payment_status');
    const cashboxRow = document.getElementById('cashbox_row');
    const paymentDetails = document.getElementById('payment_details');

    function calculatePaid() {
        let total = 0;
        document.querySelectorAll('.payment-amount').forEach(i => {
            total += parseFloat(i.value) || 0;
        });
        document.getElementById('total_paid').textContent = total.toFixed(2);
    }

    function addPaymentRow(index) {
        let div = document.createElement('div');
        div.className = 'row g-3 mb-2 payment-row';
        div.innerHTML = `
            <div class="col-md-4">
                <label>الخزنة</label>
                <select name="payments[${index}][cashbox_id]" class="form-control" required>
                    <option disabled selected>اختر الخزنة</option>
                    @foreach($cashboxes as $cb)
                        <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>المبلغ</label>
                <input type="number" name="payments[${index}][amount]" class="form-control payment-amount" step="0.01" min="0" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-payment">حذف</button>
            </div>
        `;
        paymentDetails.appendChild(div);
    }

    let paymentIndex = 0;

    paymentStatus.addEventListener('change', function () {

        if (this.value === 'paid' || this.value === 'partial') {
            cashboxRow.style.display = 'block';

            if (paymentDetails.children.length === 0) {
                addPaymentRow(paymentIndex++);
            }

        } else {
            // أجل
            cashboxRow.style.display = 'none';
            paymentDetails.innerHTML = '';
            paymentIndex = 0;
            document.getElementById('total_paid').textContent = '0';
        }
    });

    document.getElementById('add_payment').onclick = function () {
        addPaymentRow(paymentIndex++);
    };

    document.addEventListener('input', e => {
        if (e.target.classList.contains('payment-amount')) {
            calculatePaid();
        }
    });

    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-payment')) {
            e.target.closest('.payment-row').remove();
            calculatePaid();
        }
    });

    paymentStatus.dispatchEvent(new Event('change'));
    calculateTotal();
});
</script>
@endsection
