@extends('layouts.master')

@section('title','إضافة مرتجع')

@section('content')
<div class="container-fluid my-4">

<form method="POST" action="{{ route('admin.sales_returns.store') }}">
@csrf

<div class="card mb-4">
    <div class="card-body row g-3">

        <div class="col-md-4">
            <label>التاريخ</label>
            <input type="datetime-local" name="return_date"
                class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
        </div>

        <div class="col-md-4">
            <label>العميل</label>
            <select name="customer_id" class="form-select">
                <option value="">نقدي</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>الخزنة</label>
            <select name="cashbox_id" class="form-select" required>
                @foreach($cashboxes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

    </div>
</div>

<div class="card mb-4">
    <div class="card-header">المنتجات المرتجعة</div>
    <div class="card-body">

        <table class="table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>سعر المرتجع</th>
                    <th>الاجمالي</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="items">
                <tr>
                    <td>
                        <select name="items[0][product_id]" class="form-select">
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[0][qty]" class="form-control qty" min="1" value="1">
                    </td>
                    <td>
                        <input type="number" name="items[0][return_price]" class="form-control return_price" step="0.01" value="0">
                    </td>
                    <td>
                        <input type="number" class="form-control total" readonly value="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-row">حذف</button>
                    </td>
                </tr>
            </tbody>
        <button type="button" class="btn btn-primary mt-3" id="add-item">إضافة منتج آخر</button>

        <div class="row mt-3">
            <div class="col-md-6 offset-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">إجمالي المرتجع</h5>
                        <div class="input-group">
                            <input type="number" class="form-control" id="grand-total" readonly value="0.00" step="0.01">
                            <span class="input-group-text">جنيه</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </table>

    </div>
</div>

<button class="btn btn-success">حفظ المرتجع</button>
</form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1; // Start from 1 since 0 is already there

    // Function to calculate total for a row
    function calculateTotal(row) {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.return_price').value) || 0;
        const total = qty * price;
        row.querySelector('.total').value = total.toFixed(2);
        calculateGrandTotal();
    }

    // Function to calculate grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total').forEach(totalInput => {
            grandTotal += parseFloat(totalInput.value) || 0;
        });
        document.getElementById('grand-total').value = grandTotal.toFixed(2);
    }

    // Add event listeners to existing inputs
    document.querySelectorAll('.qty, .return_price').forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal(this.closest('tr'));
        });
    });

    // Add new item button functionality
    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('items');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);

        // Update names and ids
        newRow.querySelector('select').name = `items[${rowIndex}][product_id]`;
        newRow.querySelector('.qty').name = `items[${rowIndex}][qty]`;
        newRow.querySelector('.return_price').name = `items[${rowIndex}][return_price]`;
        newRow.querySelector('.qty').value = '1';
        newRow.querySelector('.return_price').value = '0';
        newRow.querySelector('.total').value = '0';

        // Add event listeners to new inputs
        newRow.querySelector('.qty').addEventListener('input', function() {
            calculateTotal(this.closest('tr'));
        });
        newRow.querySelector('.return_price').addEventListener('input', function() {
            calculateTotal(this.closest('tr'));
        });

        tbody.appendChild(newRow);
        rowIndex++;
    });

    // Delete row functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-row')) {
            const tbody = document.getElementById('items');
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                calculateGrandTotal(); // Recalculate grand total after deletion
            } else {
                alert('يجب أن يكون هناك منتج واحد على الأقل');
            }
        }
    });
});
</script>
@endsection
