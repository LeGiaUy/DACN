<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2933; }
        h1, h2, h3 { margin: 0 0 8px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .pill { padding: 4px 10px; border-radius: 999px; background: #e0f2f1; color: #00695c; font-size: 11px; }
        .grid { display: flex; flex-wrap: wrap; margin: -6px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; margin: 6px; flex: 1 1 45%; box-sizing: border-box; }
        .label { font-size: 11px; color: #6b7280; }
        .value { margin-top: 4px; font-size: 18px; font-weight: 700; color: #111827; }
        .muted { font-size: 11px; color: #6b7280; }
        .section { margin-top: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 6px 8px; border: 1px solid #e5e7eb; font-size: 11px; text-align: left; }
        th { background: #f3f4f6; }
        .status-row { font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Thống kê tổng quan</h1>
            <div class="muted">Báo cáo hệ thống quản lý bán hàng</div>
        </div>
        <div class="pill">
            Thời gian: · {{ $generated_at->format('H:i d/m/Y') }}
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="label">Tổng doanh thu</div>
            <div class="value">
                {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }} đ
            </div>
        </div>
        <div class="card">
            <div class="label">Tổng đơn hàng</div>
            <div class="value">{{ $stats['total_orders'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="label">Sản phẩm</div>
            <div class="value">{{ $stats['total_products'] ?? 0 }}</div>
            <div class="muted">
                {{ $stats['total_categories'] ?? 0 }} danh mục · {{ $stats['total_brands'] ?? 0 }} thương hiệu
            </div>
        </div>
        <div class="card">
            <div class="label">Người dùng</div>
            <div class="value">{{ $stats['total_users'] ?? 0 }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Trạng thái đơn hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>Trạng thái</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <tr class="status-row">
                    <td>Chờ xử lý</td>
                    <td>{{ $status_breakdown['pending'] ?? 0 }}</td>
                </tr>
                <tr class="status-row">
                    <td>Đang xử lý</td>
                    <td>{{ $status_breakdown['processing'] ?? 0 }}</td>
                </tr>
                <tr class="status-row">
                    <td>Đã giao</td>
                    <td>{{ $status_breakdown['delivered'] ?? 0 }}</td>
                </tr>
                <tr class="status-row">
                    <td>Đã hủy</td>
                    <td>{{ $status_breakdown['cancelled'] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
