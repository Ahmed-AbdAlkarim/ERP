@extends('layouts.master')

@section('title', 'تعديل فاتورة شراء')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">تعديل فاتورة شراء</h4>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admin.purchase_invoices.update',$invoice->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- بيانات الفاتورة --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label>رقم الفاتورة</label>
                        <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" readonly>
                        <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
                    </div>

                    <div class="col-md-3">
                        <label>المورد</label>
                        <select name="supplier_id" class="form-control" required>
                            <option disabled>اختر المورد</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $supplier->id==$invoice->supplier_id?'selected':'' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>تاريخ الفاتورة</label>
                        <input type="date" name="date" class="form-control" value="{{ $invoice->date->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label>طريقة الدفع</label>
                        <select name="payment_status" id="payment_status" class="form-control" required>
                            <option value="paid" {{ $invoice->payment_status=='paid'?'selected':'' }}>كاش</option>
                            <option value="partial" {{ $invoice->payment_status=='partial'?'selected':'' }}>دفع جزئي</option>
                            <option value="due" {{ $invoice->payment_status=='due'?'selected':'' }}>أجل</option>
                        </select>
                    </div>
                </div>

                {{-- الدفع --}}
                <div id="cashbox_row" style="display:none;">
                    <h6>تفاصيل الدفع</h6>
                    <div id="payment_details"></div>
                    <button type="button" id="add_payment" class="btn btn-secondary mb-2">+ إضافة دفعة</button>
                    <div class="mb-3"><strong>إجمالي المدفوع: </strong><span id="total_paid">0</span> ج.م</div>
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
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $i }}][product_id]" class="form-control" required>
                                    <option disabled>اختر الصنف</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $product->id==$item->product_id?'selected':'' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control quantity" value="{{ $item->quantity }}" min="1"></td>
                            <td><input type="number" name="items[{{ $i }}][purchase_price]" class="form-control price" value="{{ $item->purchase_price }}" step="0.01"></td>
                            <td><input type="number" class="form-control total" readonly value="{{ $item->total }}"></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button" id="addRow" class="btn btn-secondary mb-3">+ إضافة صنف</button>

                <hr>

                <div class="mb-3">
                    <label>مصاريف إضافية</label>
                    <input type="number" name="additional_expenses" class="form-control additional" value="{{ $invoice->additional_expenses }}" step="0.01">
                </div>

                <div class="mb-3">
                    <label>الإجمالي النهائي</label>
                    <input type="number" id="grand_total" class="form-control" value="{{ $invoice->total_cost }}" readonly>
                </div>

                <div class="mb-3">
                    <label>ملاحظات</label>
                    <textarea name="note" class="form-control">{{ $invoice->note }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">تعديل الفاتورة</button>
            </form>
        </div>
    </div>
</div>

<script>
let paymentIndex = 0;

function addPaymentRow(idx = null, cashboxId = '', amount = ''){
    let i = idx !== null ? idx : paymentIndex++;
    let html = `
        <div class="payment_row mb-2">
            <select name="payments[${i}][cashbox_id]" class="form-control d-inline w-50" required>
                <option disabled selected>اختر الخزنة</option>
                @foreach($cashboxes as $cashbox)
                    <option value="{{ $cashbox->id }}" ${cashboxId == '{{ $cashbox->id }}' ? 'selected' : ''}>{{ $cashbox->name }}</option>
                @endforeach
            </select>
            <input type="number" name="payments[${i}][amount]" class="form-control d-inline w-25" placeholder="المبلغ" value="${amount}" step="0.01" required>
            <button type="button" class="btn btn-danger btn-sm removePayment">X</button>
        </div>
    `;
    document.getElementById('payment_details').insertAdjacentHTML('beforeend', html);
    calculatePaid();
}

function calculatePaid(){
    let total = 0;
    document.querySelectorAll('#payment_details input[name$="[amount]"]').forEach(el=>{
        total += parseFloat(el.value) || 0;
    });
    document.getElementById('total_paid').textContent = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function(){
    const paymentStatus = document.getElementById('payment_status');
    const cashboxRow = document.getElementById('cashbox_row');
    const paymentDetails = document.getElementById('payment_details');

    function togglePaymentRow(){
        if(paymentStatus.value=='paid' || paymentStatus.value=='partial'){
            cashboxRow.style.display='block';
            if(paymentDetails.children.length === 0){
                @foreach($invoice->payments as $p)
                    addPaymentRow(paymentIndex++, {{ $p->cashbox_id }}, {{ $p->amount }});
                @endforeach
                if(paymentDetails.children.length === 0) addPaymentRow(paymentIndex++);
            }
        } else {
            cashboxRow.style.display='none';
            paymentDetails.innerHTML='';
            paymentIndex=0;
            document.getElementById('total_paid').textContent='0';
        }
        calculatePaid();
    }

    paymentStatus.addEventListener('change', togglePaymentRow);
    togglePaymentRow();

    document.getElementById('add_payment').addEventListener('click', ()=>addPaymentRow());

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removePayment')){
            e.target.parentElement.remove();
            calculatePaid();
        }
        if(e.target.classList.contains('removeRow')){
            e.target.closest('tr').remove();
            updateTotals();
        }
    });

    document.querySelectorAll('.quantity, .price, .additional').forEach(el=>{
        el.addEventListener('input', updateTotals);
    });

    function updateTotals(){
        let grandTotal = parseFloat(document.querySelector('.additional').value) || 0;
        document.querySelectorAll('#itemsTable tbody tr').forEach(tr=>{
            let qty = parseFloat(tr.querySelector('.quantity').value) || 0;
            let price = parseFloat(tr.querySelector('.price').value) || 0;
            let total = qty*price;
            tr.querySelector('.total').value = total.toFixed(2);
            grandTotal += total;
        });
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
        calculatePaid();
    }

    document.getElementById('addRow').addEventListener('click', function(){
        let idx = document.querySelectorAll('#itemsTable tbody tr').length;
        let row = `<tr>
            <td>
                <select name="items[${idx}][product_id]" class="form-control" required>
                    <option disabled selected>اختر الصنف</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${idx}][quantity]" class="form-control quantity" value="1" min="1"></td>
            <td><input type="number" name="items[${idx}][purchase_price]" class="form-control price" value="0" step="0.01"></td>
            <td><input type="number" class="form-control total" readonly value="0"></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
        </tr>`;
        document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', row);
        document.querySelectorAll('.quantity, .price').forEach(el=>el.addEventListener('input', updateTotals));
    });
});
</script>
@endsection
