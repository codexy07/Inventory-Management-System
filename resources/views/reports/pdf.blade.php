<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #2563eb;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-row {
            font-weight: bold;
            background-color: #f3f4f6 !important;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 10px;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Inventory Summary Report</h1>
    <p class="subtitle">Generated on {{ now()->format('F d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Status</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku ?? '—' }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>{{ $product->supplier?->name ?? '—' }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ ucfirst($product->status ?? 'active') }}</td>
                    <td>${{ number_format($product->quantity * $product->price, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="8" class="text-right">Grand Total:</td>
                <td>${{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name') }} - Inventory Management System
    </div>
</body>
</html>
