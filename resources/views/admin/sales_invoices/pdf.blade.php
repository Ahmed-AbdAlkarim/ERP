<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>فاتورة - Click Store</title>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  font-family: Tahoma, Arial, sans-serif;
  background: #f4f4f4;
  direction: rtl;
}

/* الصفحة نفسها */
.page {
  width: 210mm;
  height: 297mm;           /* 🔴 مهم */
  margin: 20px auto;
  background: #fff;
  border: 2px solid #000;  /* الإطار الحقيقي */
  position: relative;
  padding: 18mm 15mm 25mm; /* مساحة تحت لاسم المتجر */
}

/* اسم المتجر أسفل الإطار */
.page-footer-name {
  position: absolute;
  bottom: 8mm;
  left: 50%;
  transform: translateX(-50%);
  font-size: 12px;
  font-weight: bold;
  background: #fff;
  padding: 0 10px;
}

/* ===== Header ===== */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.logo {
  font-size: 26px;
  font-weight: bold;
}

.invoice-title {
  font-size: 18px;
  font-weight: bold;
  border: 2px solid #000;
  padding: 6px 16px;
}

/* ===== Info ===== */
.info-section {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
  font-size: 14px;
}

.info-box {
  width: 48%;
}

.info-box h3 {
  font-size: 15px;
  margin: 0 0 10px;
  padding-bottom: 6px;
  border-bottom: 2px solid #000;
}

.info-row {
  display: flex;
  margin-bottom: 6px;
}

.info-row span:first-child {
  width: 120px;
  font-weight: bold;
}

/* ===== Table ===== */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

thead {
  background: #000;
  color: #fff;
}

th, td {
  border: 1px solid #000;
  padding: 8px;
  text-align: center;
}

tbody tr:nth-child(even) {
  background: #f7f7f7;
}

/* ===== Totals ===== */
.totals {
  width: 40%;
  margin-top: 20px;
  margin-right: auto;
  border: 2px solid #000;
  font-size: 14px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #000;
}

.total-row.final {
  font-weight: bold;
  background: #f0f0f0;
  border-top: 2px solid #000;
}

/* ===== Print ===== */
@media print {
  body {
    background: #fff;
  }
  .page {
    margin: 0;
  }
}
</style>
</head>

<body>

<div class="page">

  <!-- Header -->
  <div class="header">
    <div class="logo">Click Store</div>
    <div class="invoice-title">فاتورة بيع</div>
  </div>

  <!-- Info -->
  <div class="info-section">

    <div class="info-box">
      <h3>بيانات الفاتورة</h3>
      <div class="info-row">
        <span>رقم الفاتورة:</span>
        <span>{{ $invoice->invoice_number }}</span>
      </div>
      <div class="info-row">
        <span>التاريخ:</span>
        <span>
          {{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d') : $invoice->invoice_date }}
        </span>
      </div>
      <div class="info-row">
        <span>الوقت:</span>
        <span>
          {{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('h:i A') : '' }}
        </span>
      </div>
    </div>

    <div class="info-box">
      <h3>بيانات العميل</h3>
      <div class="info-row">
        <span>اسم العميل:</span>
        <span>{{ $invoice->customer->name ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span>الهاتف:</span>
        <span>{{ $invoice->customer->phone ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span>العنوان:</span>
        <span>{{ $invoice->customer->address ?? '-' }}</span>
      </div>
    </div>

  </div>

  <!-- Items -->
  <table>
    <thead>
      <tr>
        <th>المنتج</th>
        <th>الكمية</th>
        <th>سعر الوحدة</th>
        <th>الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      @foreach($invoice->items as $item)
      <tr>
        <td>{{ $item->product->name ?? '-' }}</td>
        <td>{{ $item->qty }}</td>
        <td>{{ number_format($item->price,2) }} ج.م</td>
        <td>{{ number_format($item->total,2) }} ج.م</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Totals -->
  <div class="totals">
    <div class="total-row">
      <span>المجموع الفرعي</span>
      <span>{{ number_format($invoice->subtotal ?? 0,2) }} ج.م</span>
    </div>
    <div class="total-row">
      <span>الخصم</span>
      <span>- {{ number_format($invoice->discount ?? 0,2) }} ج.م</span>
    </div>
    <div class="total-row final">
      <span>الإجمالي النهائي</span>
      <span>{{ number_format($invoice->total ?? 0,2) }} ج.م</span>
    </div>
    <div class="total-row">
      <span>المدفوع</span>
      <span>{{ number_format($invoice->paid_amount ?? 0,2) }} ج.م</span>
    </div>
    <div class="total-row">
      <span>المتبقي</span>
      <span>
        {{ number_format($invoice->remaining_amount ?? ($invoice->total - ($invoice->paid_amount ?? 0)),2) }} ج.م
      </span>
    </div>
  </div>

  <!-- Footer inside page -->
  <div class="page-footer-name">Click Store</div>

</div>

</body>
</html>
