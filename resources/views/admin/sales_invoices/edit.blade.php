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
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sales-invoices.update', $invoice->id) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')

        {{-- بيانات الفاتورة --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label>العميل</label>
                <input type="text"
                       list="customers"
                       class="form-control customer-name"
                       placeholder="ابحث هنا"
                       value="{{ $invoice->customer->name ?? '' }}"
                       required>
                <input type="hidden" name="customer_id" class="customer-id" value="{{ $invoice->customer_id }}">

                <div class="mt-2">
                    <label>رصيد العميل</label>
                    <input type="text" id="customer_balance" class="form-control" readonly value="{{ $invoice->customer->balance ?? 0 }}">
                </div>
            </div>

            <div class="col-md-4">
                <label>تاريخ الفاتورة</label>
                <input type="datetime-local" name="invoice_date" class="form-control"
                       value="{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d\TH:i') }}" required>
            </div>

            <div class="col-md-4">
                <label>حالة الدفع</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>مدفوع</option>
                    <option value="partial" {{ $invoice->status == 'partial' ? 'selected' : '' }}>دفع جزئي</option>
                    <option value="installment" {{ $invoice->status == 'installment' ? 'selected' : '' }}>آجل</option>
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

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width:25%">الصنف</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>اقل سعر بيع</th>
                    <th>الإجمالي</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="items_body">
                @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>
                        <input type="text"
                               list="products"
                               class="form-control product-name"
                               placeholder="ابحث هنا"
                               value="{{ $item->product->name }}"
                               required>
                        <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $item->product_id }}">
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][qty]" class="form-control qty" min="1" value="{{ $item->qty }}">
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][price]" class="form-control price" step="0.01" min="0" value="{{ $item->price }}">
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][min_allowed_price]" class="form-control min_allowed_price" readonly value="{{ $item->product->min_allowed_price }}">
                    </td>
                    <td>
                        <input type="number" class="form-control total" readonly value="{{ $item->total }}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove_row">X</button>
                    </td>
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

{{-- المنتجات --}}
<datalist id="products">
@foreach($products as $product)
    <option
        value="{{ $product->name }}"
        data-id="{{ $product->id }}"
        data-price="{{ $product->selling_price }}"
        data-min-allowed-price="{{ $product->min_allowed_price }}">
    </option>
@endforeach
</datalist>

{{-- العملاء --}}
<datalist id="customers">
@foreach($customers as $customer)
    <option
        value="{{ $customer->name }}"
        data-id="{{ $customer->id }}"
        data-balance="{{ $customer->balance }}"
        data-phone="{{ $customer->phone }}">
    </option>
@endforeach
</datalist>

<script>
/* ================= العميل ================= */
$(document).on('input', '.customer-name', function () {
    let val = $(this).val();
    let option = $('#customers option').filter(function () {
        return this.value === val;
    }).first();

    if (option.length > 0) {
        // Matched name
        $(this).closest('.col-md-4').find('.customer-id').val(option.data('id') || '');
        let balance = option.data('balance') || 0;
        $('#customer_balance').val(parseFloat(balance).toFixed(2));
    } else {
        // Check if it's a phone
        let phoneOption = $('#customers option').filter(function () {
            return $(this).data('phone') === val;
        }).first();

        if (phoneOption.length > 0) {
            // Found by phone, set to name
            $(this).val(phoneOption.val());
            $(this).closest('.col-md-4').find('.customer-id').val(phoneOption.data('id') || '');
            let balance = phoneOption.data('balance') || 0;
            $('#customer_balance').val(parseFloat(balance).toFixed(2));
        } else {
            // No match, clear
            $(this).closest('.col-md-4').find('.customer-id').val('');
            $('#customer_balance').val('0.00');
        }
    }
});

/* ================= الأصناف ================= */
function updateRow(row) {
    let qty = parseFloat($(row).find('.qty').val()) || 0;
    let price = parseFloat($(row).find('.price').val()) || 0;
    $(row).find('.total').val((qty * price).toFixed(2));
    updateTotals();
}

function updateTotals() {
    let subtotal = 0;
    $('#items_body .total').each(function () {
        subtotal += parseFloat($(this).val()) || 0;
    });
    $('#subtotal').val(subtotal.toFixed(2));
    let discount = parseFloat($('#discount').val()) || 0;
    let total = subtotal - discount;
    $('#total').val(total.toFixed(2));
}

$('#discount').on('input', updateTotals);

$(document).on('input', '.product-name', function () {
    let val = $(this).val();
    let option = $('#products option').filter(function () {
        return this.value === val;
    }).first();

    let row = $(this).closest('tr');
    row.find('.product-id').val(option.data('id') || '');
    row.find('.price').val(option.data('price') || 0);
    row.find('.min_allowed_price').val(option.data('min-allowed-price') || 0);
    updateRow(row);
});

$(document).on('input', '.qty', function () {
    updateRow($(this).closest('tr'));
});

$(document).on('input', '.price', function () {
    let row = $(this).closest('tr');
    let price = parseFloat($(this).val()) || 0;
    let minPrice = parseFloat(row.find('.min_allowed_price').val()) || 0;
    if (price < minPrice) {
        $(this).val(minPrice.toFixed(2));
        alert('لا يمكن بيع المنتج بأقل من السعر الأدنى المسموح به');
    }
    updateRow(row);
});

$('#add_row').on('click', function () {
    let index = $('#items_body tr').length;
    let clone = $('#items_body tr:first').clone();

    clone.find('input').val('');
    clone.find('.qty').val(1);

    clone.find('.product-id').attr('name', `items[${index}][product_id]`);
    clone.find('.qty').attr('name', `items[${index}][qty]`);
    clone.find('.price').attr('name', `items[${index}][price]`);
    clone.find('.min_allowed_price').attr('name', `items[${index}][min_allowed_price]`);

    $('#items_body').append(clone);
});

/* ================= الدفع ================= */
let paymentIndex = 0;
let initialCustomerBalanceUsed = 0;

function addPaymentRow(method = 'cashbox', cashboxId = '', amount = '') {
    let html = `
        <div class="row g-3 mb-2 payment-row">
            <div class="col-md-3">
                <label>طريقة الدفع</label>
                <select name="payments[${paymentIndex}][method]" class="form-control payment-method">
                    <option value="cashbox" ${method === 'cashbox' ? 'selected' : ''}>خزنة</option>
                    <option value="customer_balance" ${method === 'customer_balance' ? 'selected' : ''}>رصيد العميل</option>
                </select>
            </div>
            <div class="col-md-3 cashbox-col">
                <label>الخزنة</label>
                <select name="payments[${paymentIndex}][cashbox_id]" class="form-control">
                    <option value="">اختر الخزنة</option>
                    @foreach($cashboxes as $cb)
                        <option value="{{ $cb->id }}" ${cashboxId == '{{ $cb->id }}' ? 'selected' : ''}>{{ $cb->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>المبلغ</label>
                <input type="number" name="payments[${paymentIndex}][amount]"
                       class="form-control payment-amount" step="0.01" min="0" value="${amount}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-payment">حذف</button>
            </div>
        </div>`;
    $('#payment_details').append(html);
    paymentIndex++;
}

$('#payment_status').on('change', function () {
    if (this.value === 'paid' || this.value === 'partial') {
        $('#cashbox_row').show();
        if ($('#payment_details').children().length === 0) {
            // Load existing payments if any
            @if(isset($existingPayments) && !empty($existingPayments))
                @foreach($existingPayments as $payment)
                    addPaymentRow('{{ $payment['method'] }}', '{{ $payment['cashbox_id'] }}', '{{ $payment['amount'] }}');
                @endforeach
            @else
                addPaymentRow();
            @endif
        }
    } else {
        $('#cashbox_row').hide();
        $('#payment_details').html('');
        $('#total_paid').text('0.00');
    }
}).trigger('change');

initialCustomerBalanceUsed = {{ collect($existingPayments)->where('method', 'customer_balance')->sum('amount') }};

$('#add_payment').on('click', addPaymentRow);

$(document).on('input', '.payment-amount', function () {
    let total = 0;
    $('.payment-amount').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total_paid').text(total.toFixed(2));
});

$(document).on('change', '.payment-method', function () {
    let row = $(this).closest('.payment-row');
    if (this.value === 'customer_balance') {
        row.find('.cashbox-col').hide();
        row.find('[name$="[cashbox_id]"]').val('');
    } else {
        row.find('.cashbox-col').show();
    }
});

$(document).on('click', '.remove-payment', function () {
    $(this).closest('.payment-row').remove();
    let total = 0;
    $('.payment-amount').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    $('#total_paid').text(total.toFixed(2));
});

/* ================= تحقق قبل الإرسال ================= */
$('#invoiceForm').on('submit', function (e) {
    let balance = parseFloat($('#customer_balance').val()) || 0;
    let used = 0;

    $('.payment-row').each(function () {
        if ($(this).find('.payment-method').val() === 'customer_balance') {
            used += parseFloat($(this).find('.payment-amount').val()) || 0;
        }
    });

    let available = balance + initialCustomerBalanceUsed;
    if (used > available) {
        alert('المبلغ المستخدم من رصيد العميل أكبر من الرصيد المتاح');
        e.preventDefault();
        return false;
    }

    // Check minimum prices
    let valid = true;
    $('#items_body tr').each(function () {
        let price = parseFloat($(this).find('.price').val()) || 0;
        let minPrice = parseFloat($(this).find('.min_allowed_price').val()) || 0;
        if (price < minPrice) {
            alert('يوجد أصناف بأسعار أقل من السعر الأدنى المسموح به');
            valid = false;
            return false; // break each
        }
    });

    if (!valid) {
        e.preventDefault();
        return false;
    }
});

updateTotals();
</script>
@endsection
