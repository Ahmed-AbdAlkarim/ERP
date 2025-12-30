@extends('layouts.master')

@section('title','تعديل فاتورة بيع')

@section('content')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container-fluid">
    <h4 class="mb-4">تعديل فاتورة بيع</h4>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif 

    <form action="{{ route('admin.sales-invoices.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4">
                <label>العميل</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">اختــر...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>تاريخ الفاتورة</label>
                <input type="datetime-local" name="invoice_date" class="form-control"
                       value="{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d\TH:i') }}" required>
            </div>

            <div class="col-md-4">
                <label>طريقة الدفع</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>كاش</option>
                    <option value="partial" {{ $invoice->status == 'partial' ? 'selected' : '' }}>دفع جزئي</option>
                    <option value="due" {{ $invoice->status == 'due' ? 'selected' : '' }}>أجل</option>
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
                        <span id="total_paid">{{ $invoice->paid_amount }}</span> ج.م
                    </div>
                </div>

        <hr>

        <h5>الأصنــاف</h5>

        <table class="table table-bordered" id="items_table">
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>أقل سعر</th>
                    <th>الإجمالي</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="items_body">
                @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>
                        <select name="items[{{ $index }}][product_id]" class="form-control product_select" required>
                            <option value="">اختــر...</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-price="{{ $product->selling_price }}" 
                                    data-min-price="{{ $product->min_allowed_price }}"
                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[{{ $index }}][qty]" class="form-control qty" min="1" value="{{ $item->qty }}" required></td>
                    <td><input type="number" name="items[{{ $index }}][price]" class="form-control price" step="0.01" value="{{ $item->price }}" required></td>
                    <td><input type="number" name="items[{{ $index }}][min_price]" class="form-control min_price" value="{{ $item->product->min_allowed_price }}" readonly></td>
                    <td><input type="number" name="items[{{ $index }}][total]" class="form-control total" value="{{ $item->total }}" readonly></td>
                    <td><button type="button" class="btn btn-danger remove_row">X</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="add_row" class="btn btn-primary mb-3">إضافة صنف</button>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <label>الإجمالي الفرعي</label>
                <input type="number" id="subtotal" name="subtotal" class="form-control" value="{{ $invoice->subtotal }}" readonly>
            </div>
            <div class="col-md-4">
                <label>الخصم</label>
                <input type="number" id="discount" name="discount" class="form-control" value="{{ $invoice->discount }}" step="0.01">
            </div>
            <div class="col-md-4">
                <label>الإجمالــي</label>
                <input type="number" id="total" name="total" class="form-control" value="{{ $invoice->total }}" readonly>
            </div>
        </div>

        <button class="btn btn-success mt-4">تحديث الفاتورة</button>

    </form>

</div>

<script>
function handleProductSelect() {
    let row = $(this).closest('tr');
    let selectedOption = $(this).find('option:selected');
    row.find('.price').val(selectedOption.attr('data-price'));
    row.find('.min_price').val(selectedOption.attr('data-min-price'));
    updateRow(row[0]);
    updateTotals();
}

$(document).ready(function() {
    $('.product_select').select2().on('select2:select', handleProductSelect);
    $('.cashbox_select').select2();
});

function updateRow(row){
    let qty = parseFloat(row.querySelector('.qty').value)||0;
    let price = parseFloat(row.querySelector('.price').value)||0;
    row.querySelector('.total').value = qty*price;
}
function updateTotals(){
    let total=0;
    document.querySelectorAll('#items_body tr').forEach(r=>total+=parseFloat(r.querySelector('.total').value)||0);
    document.getElementById('subtotal').value=total;
    let discount=parseFloat(document.getElementById('discount').value)||0;
    document.getElementById('total').value=total-discount;
}
document.getElementById('discount').addEventListener('input',updateTotals);

let rowIndex = {{ $invoice->items->count() }};
document.getElementById('add_row').addEventListener('click',()=>{
    let tbody = document.querySelector('#items_body');
    let tr = document.createElement('tr');

    tr.innerHTML = `
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-control product_select" required>
                <option value="">اختــر...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-min-price="{{ $product->min_allowed_price }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="items[${rowIndex}][qty]" class="form-control qty" value="1" min="1"></td>
        <td><input type="number" name="items[${rowIndex}][price]" class="form-control price" value="0" step="0.01"></td>
        <td><input type="number" name="items[${rowIndex}][min_price]" class="form-control min_price" readonly></td>
        <td><input type="number" class="form-control total" readonly value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
    `;
    tbody.appendChild(tr);
    // Initialize Select2 on the new select
    $(tr.querySelector('.product_select')).select2().on('select2:select', handleProductSelect);
    rowIndex++;
    updateTotals();
});

document.addEventListener('input', e => {
    if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        updateRow(e.target.closest('tr'));
        updateTotals();
    }
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('removeRow')) {
        let rows = document.querySelectorAll('#items_body tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
            updateTotals();
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

function addPaymentRow(index, cashboxId = '', amount = '') {
    let div = document.createElement('div');
    div.className = 'row g-3 mb-2 payment-row';
    div.innerHTML = `
        <div class="col-md-4">
            <label>الخزنة</label>
            <select name="payments[${index}][cashbox_id]" class="form-control" required>
                <option disabled selected>اختر الخزنة</option>
                @foreach($cashboxes as $cb)
                    <option value="{{ $cb->id }}" ${cashboxId == '{{ $cb->id }}' ? 'selected' : ''}>{{ $cb->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>المبلغ</label>
            <input type="number" name="payments[${index}][amount]" class="form-control payment-amount" step="0.01" min="0" value="${amount}" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="button" class="btn btn-danger remove-payment">حذف</button>
        </div>
    `;
    paymentDetails.appendChild(div);
    // Initialize Select2 for the new select
    $(div.querySelector('select')).select2();
}

let paymentIndex = 0;

paymentStatus.addEventListener('change', function () {
    if (this.value === 'paid' || this.value === 'partial') {
        cashboxRow.style.display = 'block';
        if (paymentDetails.children.length === 0) {
            // Load existing payments if any
            @if(isset($existingPayments) && !empty($existingPayments))
                @foreach($existingPayments as $payment)
                    addPaymentRow(paymentIndex++, '{{ $payment['cashbox_id'] }}', '{{ $payment['amount'] }}');
                @endforeach
            @else
                addPaymentRow(paymentIndex++);
            @endif
        }
    } else {
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
updateTotals();
</script>
@endsection
