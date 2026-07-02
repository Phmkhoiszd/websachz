<?php
require_once 'check_user.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Xử lý gửi thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($full_name && $email && $phone && $address) {
        $stmt = $pdo->prepare("select c.quantity, b.book_id, b.book_name, b.price from carts c join books b on c.book_id = b.book_id where c.user_id = ?");
        $stmt->execute([$user_id]);
        $cartItems = $stmt->fetchAll();

        if (count($cartItems) > 0) {
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $pdo->beginTransaction();
            try {
                $stmtOrder = $pdo->prepare("insert into orders (user_id, full_name, email, phone, address, total_amount, status) values (?, ?, ?, ?, ?, ?, 'pending')");
                $stmtOrder->execute([$user_id, $full_name, $email, $phone, $address, $totalPrice]);
                $order_id = $pdo->lastInsertId();

                $stmtItem = $pdo->prepare("insert into order_items (order_id, book_id, quantity, price) values (?, ?, ?, ?)");
                foreach ($cartItems as $item) {
                    $stmtItem->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
                }

                $stmtDelete = $pdo->prepare("delete from carts where user_id = ?");
                $stmtDelete->execute([$user_id]);

                $pdo->commit();
                header("Location: order_success.php?id=" . $order_id);
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = '<div class="alert alert-danger">Có lỗi trong quá trình thanh toán. Vui lòng thử lại.</div>';
            }
        } else {
            $message = '<div class="alert alert-warning">Giỏ hàng của bạn đang trống.</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Vui lòng điền đầy đủ thông tin giao hàng.</div>';
    }
}

// Lấy giỏ hàng và tổng tiền
$stmt = $pdo->prepare("select c.quantity, b.book_name, b.price from carts c join books b on c.book_id = b.book_id where c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

$userInfo = [
    'full_name' => $_SESSION['full_name'] ?? '',
    'email' => $_SESSION['username'] ?? '',
    'phone' => '',
    'address' => ''
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - WorldOfBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <?= $message ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control"
                                    value="<?= htmlspecialchars($userInfo['full_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($userInfo['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control"
                                    value="<?= htmlspecialchars($userInfo['phone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea name="address" class="form-control" rows="4"
                                    required><?= htmlspecialchars($userInfo['address']) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-bag-check me-1"></i> Xác
                                nhận thanh toán</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($cartItems) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($cartItems as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item['book_name']) ?></div>
                                            <small class="text-muted">x<?= $item['quantity'] ?></small>
                                        </div>
                                        <span
                                            class="text-danger fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                            đ</span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <strong>Tổng cộng</strong>
                                    <strong class="text-danger"><?= number_format($totalPrice, 0, ',', '.') ?> đ</strong>
                                </li>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p class="text-muted mb-3">Giỏ hàng của bạn đang trống.</p>
                                <a href="../index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>