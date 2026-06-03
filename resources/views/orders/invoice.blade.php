<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #1e293b; background: white; padding: 40px; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #d97706; }
        .invoice-title { font-size: 32px; font-weight: 800; color: #d97706; }
        .invoice-subtitle { font-size: 14px; color: #64748b; margin-top: 5px; }
        .company-name { font-size: 20px; font-weight: 700; color: #0f172a; }
        .company-detail { font-size: 12px; color: #64748b; line-height: 1.6; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { padding: 15px 20px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #d97706; }
        .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 600; }
        .info-value { font-size: 14px; font-weight: 600; color: #1e293b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        thead th { background: #0f172a; color: white; padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th:last-child { text-align: right; }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        tbody td:last-child { text-align: right; font-weight: 600; }
        .totals { display: flex; justify-content: flex-end; }
        .totals-box { width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; }
        .totals-row.total { font-size: 18px; font-weight: 800; color: #d97706; border-top: 2px solid #0f172a; padding-top: 12px; margin-top: 4px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-processing { background: #dbeafe; color: #2563eb; }
        .status-completed { background: #d1fae5; color: #059669; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-subtitle">Order #{{ $order->id }}</div>
        </div>
        <div style="text-align: right;">
            <div class="company-name">InventoryMS</div>
            <div class="company-detail">
                Inventory Management System<br>
                invoice@inventoryms.com
            </div>
        </div>
    </div>

    <div class="invoice-info">
        <div class="info-box">
            <div class="info-label">Bill To</div>
            <div class="info-value">{{ $order->user->name }}</div>
            <div class="company-detail">{{ $order->user->email }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Invoice Date</div>
            <div class="info-value">{{ $order->created_at->format('M d, Y') }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->product->name ?? 'N/A' }}</td>
                <td>{{ $order->product->sku ?? 'N/A' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ number_format($order->unit_price, 2) }}</td>
                <td>${{ number_format($order->total_price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-box">
            <div class="totals-row total">
                <span>Total</span>
                <span>${{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>
    </div>

    @if($order->notes)
    <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border-radius: 10px;">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 600; margin-bottom: 5px;">Notes</div>
        <div style="font-size: 13px; color: #475569;">{{ $order->notes }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top: 5px;">InventoryMS — Inventory Management System</p>
    </div>
</body>
</html>
