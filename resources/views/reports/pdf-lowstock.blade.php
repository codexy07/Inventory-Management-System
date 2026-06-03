<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; color: #1e293b; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 3px solid #dc2626; }
        .title { font-size: 28px; font-weight: 800; color: #dc2626; }
        .subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
        .company { text-align: right; font-size: 12px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        thead th { background: #dc2626; color: white; padding: 10px 14px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .badge-danger { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .badge-warning { background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Low Stock Alert Report</div>
            <div class="subtitle">Generated on {{ now()->format('F j, Y') }}</div>
        </div>
        <div class="company">InventoryMS<br>Inventory Management System</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $product->name }}</strong></td>
                <td>{{ $product->sku ?? '—' }}</td>
                <td>{{ $product->category?->name ?? '—' }}</td>
                <td><strong>{{ $product->quantity }}</strong></td>
                <td>{{ $product->reorder_level }}</td>
                <td>
                    @if($product->quantity <= 0)
                        <span class="badge-danger">OUT OF STOCK</span>
                    @else
                        <span class="badge-warning">LOW STOCK</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Low Stock Items: {{ $products->count() }}</p>
        <p style="margin-top:4px;">InventoryMS — Inventory Management System</p>
    </div>
</body>
</html>
