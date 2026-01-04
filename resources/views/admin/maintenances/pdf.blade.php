<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال صيانة</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .receipt-container {
            width: 100%;
        }
        .receipt {
            width: 100%;
            padding: 8px;
            border: 1px solid #000;
            box-sizing: border-box;
            margin: 2px 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
        }
        .details {
            margin-bottom: 10px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 2px 5px;
            border: 1px solid #ddd;
        }
        .details .label {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 30%;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 10px;
            font-size: 10px;
        }
        .cut-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- First copy for customer -->
        <div class="receipt">
            <div class="header">
                <h2>إيصال صيانة</h2>
                <p>رقم الصيانة: #{{ $maintenance->id }}</p>
            </div>

            <div class="details">
                <table>
                    <tr>
                        <td class="label">العميل:</td>
                        <td>{{ $maintenance->customer->name ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">رقم الهاتف:</td>
                        <td>{{ $maintenance->customer->phone ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">نوع الجهاز:</td>
                        <td>{{ $maintenance->device_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">نوع العطل:</td>
                        <td>{{ $maintenance->fault_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">التكلفة:</td>
                        <td>{{ number_format($maintenance->cost, 2) }} ج.م</td>
                    </tr>
                    <tr>
                        <td class="label">تاريخ التسليم:</td>
                        <td>{{ $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الحالة:</td>
                        <td>
                            @switch($maintenance->status)
                                @case('pending') في الانتظار @break
                                @case('in_progress') قيد التنفيذ @break
                                @case('completed') مكتمل @break
                                @case('delivered') تم التسليم @break
                            @endswitch
                        </td>
                    </tr>
                    @if($maintenance->notes)
                    <tr>
                        <td class="label">الملاحظات:</td>
                        <td>{{ $maintenance->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="footer">
                <p>تاريخ الإنشاء: {{ $maintenance->created_at->format('Y-m-d H:i') }}</p>
                <p>شكراً لكم - نتمنى لكم خدمة ممتازة click store</p>
            </div>
        </div>

        <div class="cut-line"></div>

        <!-- Second copy for shop -->
        <div class="receipt">
            <div class="header">
                <h2>إيصال صيانة</h2>
                <p>رقم الصيانة: #{{ $maintenance->id }}</p>
            </div>

            <div class="details">
                <table>
                    <tr>
                        <td class="label">العميل:</td>
                        <td>{{ $maintenance->customer->name ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">رقم الهاتف:</td>
                        <td>{{ $maintenance->customer->phone ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">نوع الجهاز:</td>
                        <td>{{ $maintenance->device_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">نوع العطل:</td>
                        <td>{{ $maintenance->fault_type }}</td>
                    </tr>
                    <tr>
                        <td class="label">التكلفة:</td>
                        <td>{{ number_format($maintenance->cost, 2) }} ج.م</td>
                    </tr>
                    <tr>
                        <td class="label">تاريخ التسليم:</td>
                        <td>{{ $maintenance->delivery_date ? $maintenance->delivery_date->format('Y-m-d') : 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الحالة:</td>
                        <td>
                            @switch($maintenance->status)
                                @case('pending') في الانتظار @break
                                @case('in_progress') قيد التنفيذ @break
                                @case('completed') مكتمل @break
                                @case('delivered') تم التسليم @break
                            @endswitch
                        </td>
                    </tr>
                    @if($maintenance->notes)
                    <tr>
                        <td class="label">الملاحظات:</td>
                        <td>{{ $maintenance->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="footer">
                <p>تاريخ الإنشاء: {{ $maintenance->created_at->format('Y-m-d H:i') }}</p>
                <p>شكراً لكم - نتمنى لكم خدمة ممتازة click store</p>
            </div>
        </div>
    </div>

    
</body>
</html>
