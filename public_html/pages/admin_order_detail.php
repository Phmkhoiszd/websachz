<?php
require_once 'check_admin.php';
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_orders.php");
    exit;
}

$order_id = $_GET['id'];

// Lấy thông tin đơn hàng tổng quát
$stmt = $pdo->prepare("select * from orders where order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Đơn hàng không tồn tại!");
}

// Lấy danh sách sản phẩm thuộc đơn hàng (JOIN với bảng books để lấy tên sách, hình ảnh)
$stmt = $pdo->prepare("
    select oi.*, b.book_name, b.image_path 
    from order_items oi 
    join books b on oi.book_id = b.book_id 
    where oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?= $order['order_id'] ?> - WorldOfBook</title>
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

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">📚 WorldOfBook</div>
        <nav class="nav flex-column">
            <a href="admin_dashboard.php" class="nav-link">
                <i class="bi bi-graph-up"></i> Dashboard
            </a>
            <a href="admin_orders.php" class="nav-link active">
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
        <div class="mb-3">
            <a href="admin_orders.php" class="btn btn-secondary">⬅️ Quay lại danh sách</a>
        </div>

        <h1 class="mb-4">Chi Tiết Đơn Hàng #<?= $order['order_id'] ?></h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">Thông tin nhận hàng</div>
                    <div class="card-body">
                        <p><strong>Khách hàng:</strong><br><?= htmlspecialchars($order['full_name']) ?></p>
                        <p><strong>Điện thoại:</strong><br><?= htmlspecialchars($order['phone']) ?></p>
                        <p><strong>Email:</strong><br><?= htmlspecialchars($order['email']) ?></p>
                        <p><strong>Địa chỉ giao hàng:</strong><br><?= htmlspecialchars($order['address']) ?></p>
                        <p><strong>Ngày đặt hàng:</strong><br><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                        <p><strong>Trạng thái:</strong><br>
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
                            <span class="badge <?= $badge_class ?>"
                                style="font-size: 0.9rem; padding: 0.5rem;"><?= $order['status'] ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Sản phẩm đã đặt</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Tên sách</th>
                                        <th class="text-end">Giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt=""
                                                    style="width: 50px; height: 60px; object-fit: cover; border-radius: 4px;">
                                            </td>
                                            <td><?= htmlspecialchars($item['book_name']) ?></td>
                                            <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                            <td class="text-center"><span
                                                    class="badge bg-light text-dark"><?= $item['quantity'] ?></span></td>
                                            <td class="text-end fw-bold">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-end">TỔNG GIÁ TRỊ ĐƠN HÀNG:</td>
                                        <td class="text-end text-danger" style="font-size: 1.1rem;">
                                            <?= number_format($order['total_amount'], 0, ',', '.') ?> đ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>