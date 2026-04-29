<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة الطلب #{{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
      @font-face {
        font-family: 'Cairo';
        src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
    }
        body {
            font-family: "Tahoma", Arial, sans-serif;
            background: #f7f7f7;
            padding: 30px;
            direction: rtl;
            text-align: right;
        }

        .invoice-container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2, h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .box {
            background: #fafafa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #e5e5e5;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .item img {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .item-details {
            flex: 1;
        }

        .total-box {
            background: #e8f7e8;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #c8e6c9;
            font-size: 18px;
            font-weight: bold;
            color: #2e7d32;
        }
        

    </style>
</head>
<body>

<div class="invoice-container">

    <h2>فاتورة الطلب رقم #{{ $order->order_number }}</h2>

    <div class="box">
        <p><strong>العميل:</strong> {{ $order->customer_name }}</p>
        <p><strong>الجوال:</strong> {{ $order->customer_phone }}</p>
        <p><strong>البريد الإلكتروني:</strong> {{ $order->customer_email }}</p>
        <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
    </div>

    <h3>المنتجات</h3>

    @foreach($order->items as $item)
        <div class="item">

            <!-- صورة المنتج -->
            <img src="{{ $item->product->images[0]->url ?? 'https://via.placeholder.com/70' }}" alt="صورة المنتج">

            <div class="item-details">
                <p><strong>{{ $item->product_name }}</strong></p>
                <p>الكمية: {{ $item->quantity }}</p>
                <p>السعر: {{ $item->unit_price }} ر.س</p>
            </div>

            <div>
                <strong>{{ $item->total_price }} ر.س</strong>
            </div>

        </div>
    @endforeach

    <div class="total-box">
        الإجمالي النهائي: {{ $order->total }} ر.س
    </div>

</div>

</body>
</html>
