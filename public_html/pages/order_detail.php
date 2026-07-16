<?php
require_once 'check_user.php';
require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$order_id = intval($_GET['id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($is_admin) {
    $stmt = $pdo->prepare("select * from orders where order_id = ?");
    $stmt->execute([$order_id]);
} else {
    $stmt = $pdo->prepare("select * from orders where order_id = ? and user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
}

$order = $stmt->fetch();

if (!$order) {
    header("Location: ../index.php");
    exit;
}

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
    <title>Chi tiết đơn hàng #<?= $order['order_id'] ?> - WorldOfBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Chi tiết đơn hàng #<?= $order['order_id'] ?></h2>
            <a href="../index.php" class="btn btn-outline-secondary">Về trang chủ</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Thông tin nhận hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Khách hàng:</strong><br><?= htmlspecialchars($order['full_name']) ?></p>
                        <p class="mb-2"><strong>Điện thoại:</strong><br><?= htmlspecialchars($order['phone']) ?></p>
                        <p class="mb-2"><strong>Email:</strong><br><?= htmlspecialchars($order['email']) ?></p>
                        <p class="mb-2"><strong>Địa chỉ:</strong><br><?= htmlspecialchars($order['address']) ?></p>
                        <p class="mb-2"><strong>Ngày đặt:</strong><br><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                        <p class="mb-0"><strong>Trạng thái:</strong><br>
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
                            <span class="badge <?= $badge_class ?>" style="font-size: 0.9rem; padding: 0.5rem;">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Sản phẩm đã đặt</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                                            <td class="text-center"><span class="badge bg-light text-dark"><?= $item['quantity'] ?></span></td>
                                            <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-end">Tổng tiền:</td>
                                        <td class="text-end text-danger" style="font-size: 1.05rem;">
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
</body>

</html>
