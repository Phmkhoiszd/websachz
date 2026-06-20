<?php
require_once 'check_user.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Xử lý cập nhật số lượng hoặc xóa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        $cart_id = intval($_POST['cart_id']);
        $quantity = max(1, intval($_POST['quantity']));
        $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE cart_id = ? AND user_id = ?");
        $stmt->execute([$quantity, $cart_id, $user_id]);
        $message = '<div class="alert alert-success">Cập nhật giỏ hàng thành công!</div>';
    }

    if (isset($_POST['remove_cart'])) {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $pdo->prepare("DELETE FROM carts WHERE cart_id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        $message = '<div class="alert alert-success">Đã xóa sản phẩm khỏi giỏ hàng.</div>';
    }
}

// Lấy danh sách sản phẩm trong giỏ hàng của user
$stmt = $pdo->prepare("SELECT c.cart_id, c.quantity, b.book_id, b.book_name, b.price, b.image_path FROM carts c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - WorldOfBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .cart-card { border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .cart-item-img { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; }
        .table thead th { border-bottom: 2px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Giỏ hàng</h2>
            <a href="../index.php" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
        </div>

        <?= $message ?>

        <div class="card cart-card p-4 mb-4">
            <?php if (count($cartItems) > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="" class="cart-item-img">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['book_name']) ?></h6>
                                                <small class="text-muted">ID sách: <?= $item['book_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm" style="width: 80px;">
                                            <button type="submit" name="update_cart" class="btn btn-sm btn-primary">Cập nhật</button>
                                        </form>
                                    </td>
                                    <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                    <td class="text-end"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                                    <td class="text-end">
                                        <form method="POST">
                                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                            <button type="submit" name="remove_cart" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4">
                    <div class="text-muted">Tổng cộng <strong><?= count($cartItems) ?></strong> sản phẩm</div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="text-end">
                            <div class="text-muted">Tổng thanh toán</div>
                            <div class="fs-4 fw-bold text-danger"><?= number_format($totalPrice, 0, ',', '.') ?> đ</div>
                        </div>
                        <a href="checkout.php" class="btn btn-success btn-lg">Thanh toán</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <img src="../images/emptycart.jpg" alt="Giỏ hàng trống" class="img-fluid mb-3" style="width: 140px; opacity: 0.6;">
                    <h5>Giỏ hàng của bạn đang trống</h5>
                    <p class="text-muted">Thêm sách vào giỏ để tiếp tục mua sắm.</p>
                    <a href="../index.php" class="btn btn-primary">Mua sắm ngay</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>