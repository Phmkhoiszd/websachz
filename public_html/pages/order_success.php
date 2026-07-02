<?php
require_once 'check_user.php';
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: cart.php");
    exit;
}

$order_id = intval($_GET['id']);
$stmt = $pdo->prepare("select * from orders where order_id = ? and user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="card shadow-sm border-0 mx-auto" style="max-width: 700px;">
            <div class="card-body text-center p-5">
                <div class="mb-4 text-success">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                </div>
                <h2 class="fw-bold mb-3">Thanh toán thành công!</h2>
                <p class="mb-3">Cảm ơn bạn đã mua sắm tại WorldOfBook. Đơn hàng của bạn đã được ghi nhận và chúng tôi sẽ
                    xử lý trong thời gian sớm nhất.</p>
                <p class="mb-1"><strong>Mã đơn hàng:</strong> #<?= $order['order_id'] ?></p>
                <p class="mb-4"><strong>Tổng tiền:</strong> <?= number_format($order['total_amount'], 0, ',', '.') ?> đ
                </p>
                <a href="../index.php" class="btn btn-primary me-2">Về trang chủ</a>
                <a href="admin_order_detail.php?id=<?= $order['order_id'] ?>" class="btn btn-outline-secondary">Xem chi
                    tiết</a>
            </div>
        </div>
    </div>
</body>

</html>