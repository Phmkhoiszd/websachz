<?php
require_once 'check_admin.php';
require_once 'db.php';

// Xử lý cập nhật trạng thái đơn hàng nếu có yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("update orders set status = ? where order_id = ?");
    $stmt->execute([$new_status, $order_id]);
    header("Location: admin_orders.php?msg=Cập nhật thành công!");
    exit;
}

// Lấy danh sách toàn bộ đơn hàng
$stmt = $pdo->query("select * from orders order by created_at desc");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn Hàng - WorldOfBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; position: fixed; left: 0; top: 0; width: 250px; padding: 20px 0; }
        .sidebar .logo { text-align: center; color: white; font-size: 24px; font-weight: bold; margin-bottom: 30px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); margin: 10px 0; padding: 12px 20px; border-radius: 8px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 250px; padding: 30px; background: #f8f9fa; min-height: 100vh; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; }
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
        <h1 class="mb-4"><i class="bi bi-box-seam"></i> Quản lý Đơn Hàng</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách Đơn Hàng (<?= count($orders) ?> đơn)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách Hàng</th>
                                <th>Thông Tin Liên Hệ</th>
                                <th>Tổng Tiền</th>
                                <th>Ngày Đặt</th>
                                <th>Trạng Thái</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?= $order['order_id'] ?></strong></td>
                                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                                    <td>
                                        <small>
                                            📞 <?= htmlspecialchars($order['phone']) ?><br>
                                            ✉️ <?= htmlspecialchars($order['email']) ?><br>
                                            📍 <?= htmlspecialchars(substr($order['address'], 0, 30)) ?>...
                                        </small>
                                    </td>
                                    <td class="text-danger fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <?php 
                                            $badge_class = 'bg-secondary';
                                            if ($order['status'] == 'pending') $badge_class = 'bg-warning text-dark';
                                            elseif ($order['status'] == 'processing') $badge_class = 'bg-info text-white';
                                            elseif ($order['status'] == 'completed') $badge_class = 'bg-success';
                                            elseif ($order['status'] == 'cancelled') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $order['status'] ?></span>
                                    </td>
                                    <td>
                                        <form action="" method="POST" class="d-inline-block me-2">
                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm" style="width: 120px; display: inline-block;" onchange="this.form.submit()">
                                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Đang giao</option>
                                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Đã giao</option>
                                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy đơn</option>
                                            </select>
                                        </form>
                                        <a href="admin_order_detail.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>