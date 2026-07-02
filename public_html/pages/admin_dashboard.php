<?php
require_once 'check_admin.php';
require_once 'db.php';

// Lấy thống kê
$stats = [];

// Tổng số đơn hàng
$stmt = $pdo->query("select count(*) as total from orders");
$stats['total_orders'] = $stmt->fetch()['total'];

// Tổng doanh thu
$stmt = $pdo->query("select sum(total_amount) as revenue from orders where status != 'cancelled'");
$result = $stmt->fetch();
$stats['revenue'] = $result['revenue'] ?? 0;

// Số đơn chưa xử lý
$stmt = $pdo->query("select count(*) as pending from orders where status = 'pending'");
$stats['pending'] = $stmt->fetch()['pending'];

// Số sách trong kho
$stmt = $pdo->query("select count(*) as total from books");
$stats['total_books'] = $stmt->fetch()['total'];

// Đơn hàng gần đây
$stmt = $pdo->query("select * from orders order by created_at desc limit 5");
$recent_orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WorldOfBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding: 20px 0;
        }

        .sidebar .logo {
            text-align: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin: 10px 0;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #999;
            font-size: 14px;
            margin-top: 5px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">📚 WorldOfBook</div>
        <nav class="nav flex-column">
            <a href="admin_dashboard.php" class="nav-link active">
                <i class="bi bi-graph-up"></i> Dashboard
            </a>
            <a href="admin_orders.php" class="nav-link">
                <i class="bi bi-box-seam"></i> Quản lý đơn hàng
            </a>
            <a href="admin_books.php" class="nav-link">
                <i class="bi bi-book"></i> Quản lý sách
            </a>
            <a href="admin_categories.php" class="nav-link">
                <i class="bi bi-list-ul"></i> Danh mục
            </a>
            <hr style="background: rgba(255,255,255,0.2);">
            <a href="logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header-top">
            <div>
                <h1 class="mb-0">Dashboard</h1>
                <small class="text-muted">Chào mừng, <?= htmlspecialchars($_SESSION['username']) ?>!</small>
            </div>
            <div class="user-info">
                <span><?= date('d/m/Y H:i') ?></span>
                <div
                    style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-value"><?= $stats['total_orders'] ?></div>
                    <div class="stat-label">Tổng số đơn hàng</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value"><?= number_format($stats['revenue'], 0, ',', '.') ?></div>
                    <div class="stat-label">Doanh thu (đ)</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value"><?= $stats['pending'] ?></div>
                    <div class="stat-label">Đơn chờ xử lý</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?= $stats['total_books'] ?></div>
                    <div class="stat-label">Tổng số sách</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Đơn hàng gần đây</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã ĐH</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['order_id'] ?></strong></td>
                                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                                            <td><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</td>
                                            <td>
                                                <?php
                                                $badge_class = 'bg-secondary';
                                                if ($order['status'] == 'pending')
                                                    $badge_class = 'bg-warning text-dark';
                                                elseif ($order['status'] == 'processing')
                                                    $badge_class = 'bg-info text-white';
                                                elseif ($order['status'] == 'completed')
                                                    $badge_class = 'bg-success';
                                                elseif ($order['status'] == 'cancelled')
                                                    $badge_class = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= $order['status'] ?></span>
                                            </td>
                                            <td>
                                                <a href="admin_order_detail.php?id=<?= $order['order_id'] ?>"
                                                    class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin hệ thống</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Phiên bản:</strong> 1.0.0</p>
                        <p><strong>Database:</strong> worldofbook</p>
                        <p><strong>Ngôn ngữ:</strong> PHP 8.2</p>
                        <p><strong>Framework:</strong> Bootstrap 5</p>
                        <hr>
                        <small class="text-muted">Hệ thống quản lý cửa hàng sách trực tuyến</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>