@extends('layouts.master')

@section('title','إنشاء فاتورة بيع')

@section('content')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="container-fluid">
    <h4 class="mb-4">إنشاء فاتورة بيع</h4>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.sales-invoices.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label>العميل</label>
                <select name="customer_id" class="form-control">
                    <option value="">اختــر...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>تاريخ الفاتورة</label>
                <input type="datetime-local" name="invoice_date" class="form-control" required>
            </div>

            <div class="col-md-4">
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

        <h5>الأصنــاف</h5>
        <table class="table table-bordered" id="items_table">
            <thead>
                <tr>
                    <th style="width: 25%;">الصنف</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>أقل سعر</th>
                    <th>الإجمالي</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="items_body">
                <tr>
                    <td>
                        <select name="items[0][product_id]" class="form-control product_select" required>
                            <option value="">اختــر...</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-price="{{ $product->selling_price }}" 
                                        data-min-price="{{ $product->min_allowed_price }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[0][qty]" class="form-control qty" min="1" value="1" required></td>
                    <td><input type="number" name="items[0][price]" class="form-control price" step="0.01" required readonly></td>
                    <td><input type="number" name="items[0][min_price]" class="form-control min_price" readonly></td>
                    <td><input type="number" name="items[0][total]" class="form-control total" readonly></td>
                    <td><button type="button" class="btn btn-danger remove_row">X</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add_row" class="btn btn-primary mb-3">إضافة صنف</button>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <label>الإجمالي الفرعي</label>
                <input type="number" id="subtotal" name="subtotal" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>الخصم</label>
                <input type="number" id="discount" name="discount" class="form-control" value="0" step="0.01">
            </div>
            <div class="col-md-4">
                <label>الإجمالي النهائي</label>
                <input type="number" id="total" name="total" class="form-control" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">حفظ الفاتورة</button>
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

    // Add event listener for qty changes on existing rows
    $('.qty').on('input', function() {
        updateRow($(this).closest('tr')[0]);
        updateTotals();
    });
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
document.getElementById('add_row').addEventListener('click',()=>{
    let index=document.querySelectorAll('#items_body tr').length;
    let clone=document.querySelector('#items_body tr').cloneNode(true);
    clone.querySelectorAll('input,select').forEach(el=>{
        if(el.name) el.name=el.name.replace(/\d+/,index);
        if(el.classList.contains('qty')) el.value=1;
        if(el.classList.contains('price')||el.classList.contains('total')) el.value=0;
    });
    document.getElementById('items_body').appendChild(clone);
    // Initialize Select2 on the new product select
    clone.querySelector('.product_select').classList.remove('select2-hidden-accessible');
    $(clone.querySelector('.product_select')).select2().on('select2:select', handleProductSelect);
    // Add event listener for qty changes on the new row
    $(clone.querySelector('.qty')).on('input', function() {
        updateRow($(this).closest('tr')[0]);
        updateTotals();
    });
});
document.getElementById('items_body').addEventListener('click',e=>{
    if(e.target.classList.contains('remove_row')){
        let rows=document.querySelectorAll('#items_body tr');
        if(rows.length>1)e.target.closest('tr').remove();
        updateTotals();
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
        // Initialize Select2 for the new select
        $(div.querySelector('select')).select2();
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
    updateTotals();
</script>
@endsection
