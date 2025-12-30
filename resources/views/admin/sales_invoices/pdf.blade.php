<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فاتورة - Click Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* عام */
    body {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Tahoma, Arial, sans-serif;
      background: #f5f5f5;
      color: #000;
      direction: rtl;
    }

    .page-wrapper {
      width: 100%;
      padding: 20px;
    }

    .invoice-page {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      padding: 20mm;
      box-sizing: border-box;
      border: 3px solid #000;
      position: relative;
    }

    /* داخلية بسيطة بدل pseudo-element (mpdf أفضل بدون ::before في بعض الأحيان) */
    .inner-border {
      border: 1px solid #cccccc;
      padding: 8mm;
      height: 100%;
      box-sizing: border-box;
    }

    .invoice-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #000;
    }

    .company-name {
      font-size: 32px;
      font-weight: 700;
      margin: 0;
    }

    .invoice-info, .customer-info {
      margin-top: 14px;
      margin-bottom: 14px;
      font-size: 14px;
    }

    .info-row, .customer-row {
      width: 100%;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .info-label {
      display: inline-block;
      min-width: 140px;
      font-weight: 700;
      color: #000;
    }

    .info-value {
      display: inline-block;
      color: #333;
      border-bottom: 1px solid #ccc;
      padding-bottom: 3px;
      min-width: 200px;
    }

    .section-divider {
      height: 1px;
      background: #cccccc;
      margin: 14px 0;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin: 18px 0;
      border: 1px solid #000;
      font-size: 13px;
    }

    .items-table thead {
      background: #e8e8e8;
    }

    .items-table th {
      padding: 10px;
      text-align: center;
      font-weight: 700;
      border: 1px solid #000;
    }

    .items-table td {
      padding: 8px;
      text-align: center;
      border: 1px solid #ccc;
      color: #333;
    }

    .totals-section {
      margin-top: 18px;
      width: 100%;
    }

    .totals-box {
      width: 360px;
      border: 1px solid #000;
      float: left;
      background: #fff;
      font-size: 14px;
    }

    .total-row {
      padding: 10px 12px;
      border-bottom: 1px solid #ccc;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .total-row.header {
      background: #e8e8e8;
      font-weight: 700;
      border-bottom: 2px solid #000;
    }

    .final-total-row {
      background: #f5f5f5;
      font-weight: 700;
      border-top: 2px solid #000;
    }

    .total-label { font-weight: 700; color: #000; }
    .total-value { font-weight: 700; color: #222; }

    .invoice-footer {
      clear: both;
      margin-top: 40px;
      text-align: center;
      padding-top: 12px;
      border-top: 2px solid #000;
      font-size: 13px;
    }

    /* تحسين للـ print */
    @media print {
      body { background: #fff; }
      .page-wrapper { padding: 0; }
      .invoice-page { box-shadow: none; width: 100%; min-height: auto; }
    }
  </style>
</head>
<body>
  <div class="page-wrapper">
    <div class="invoice-page">
      <div class="inner-border">

        <!-- Header -->
        <div class="invoice-header">
          <h1 class="company-name">Click Store</h1>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
          <div class="info-row">
            <span class="info-label">رقم الفاتورة:</span>
            <span class="info-value">{{ $invoice->invoice_number }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">التاريخ:</span>
            <span class="info-value">
              {{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d H:i') : $invoice->invoice_date }}
            </span>
          </div>

          <div class="info-row">
            <span class="info-label">الوقت:</span>
            <span class="info-value">
              {{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('h:i A') : '' }}
            </span>
          </div>
        </div>

        <div class="section-divider"></div>

        <!-- Customer Info -->
        <div class="customer-info">
          <div class="customer-row">
            <span class="info-label">اسم العميل:</span>
            <span class="info-value">{{ $invoice->customer->name ?? '-' }}</span>
          </div>

          <div class="customer-row">
            <span class="info-label">الهاتف:</span>
            <span class="info-value">{{ $invoice->customer->phone ?? '-' }}</span>
          </div>

          <div class="customer-row">
            <span class="info-label">العنوان:</span>
            <span class="info-value">{{ $invoice->customer->address ?? '-' }}</span>
          </div>
        </div>

        <!-- Items Table -->
        <table class="items-table" cellpadding="0" cellspacing="0">
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
                <td>{{ number_format($item->price, 2) }} ج.م</td>
                <td>{{ number_format($item->total, 2) }} ج.م</td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
          <div class="totals-box">
            <div class="total-row header">
              <span class="total-label">المجموع الفرعي:</span>
              <span class="total-value">{{ number_format($invoice->subtotal ?? 0, 2) }} ج.م</span>
            </div>

            <div class="total-row">
              <span class="total-label">الخصم:</span>
              <span class="total-value">- {{ number_format($invoice->discount ?? 0, 2) }} ج.م</span>
            </div>

            <div class="total-row final-total-row">
              <span class="total-label">الإجمالي النهائي:</span>
              <span class="total-value">{{ number_format($invoice->total ?? 0, 2) }} ج.م</span>
            </div>

            <div class="total-row">
              <span class="total-label">المبلغ المدفوع:</span>
              <span class="total-value">{{ number_format($invoice->paid_amount ?? 0, 2) }} ج.م</span>
            </div>

            <div class="total-row">
              <span class="total-label">المبلغ المتبقي:</span>
              <span class="total-value">{{ number_format($invoice->remaining_amount ?? ($invoice->total - ($invoice->paid_amount ?? 0)), 2) }} ج.م</span>
            </div>

          </div> <!-- end totals-box -->

          <div style="clear: both;"></div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
          <p class="footer-text">شكراً لتعاملكم معنا — Click Store</p>
        </div>

      </div> <!-- end inner-border -->
    </div> <!-- end invoice-page -->
  </div> <!-- end page-wrapper -->
</body>
</html>
