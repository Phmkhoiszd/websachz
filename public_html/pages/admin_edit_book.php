<?php
require_once 'check_admin.php';
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_books.php");
    exit;
}

$book_id = intval($_GET['id']);
$message = '';

// Lấy thông tin sách
$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    header("Location: admin_books.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_book'])) {
    $book_name = trim($_POST['book_name']);
    $author = trim($_POST['author']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $image_path = trim($_POST['image_path']);

    if ($book_name && $author && $price > 0) {
        $stmt = $pdo->prepare("UPDATE books SET book_name = ?, author = ?, price = ?, category_id = ?, image_path = ? WHERE book_id = ?");
        if ($stmt->execute([$book_name, $author, $price, $category_id, $image_path, $book_id])) {
            $message = '<div class="alert alert-success">Cập nhật sách thành công!</div>';
            // Reload book data
            $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch();
        }
    }
}

// Lấy danh mục
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sách - WorldOfBook</title>
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
            <a href="admin_orders.php" class="nav-link">
                <i class="bi bi-box-seam"></i> Quản lý đơn hàng
            </a>
            <a href="admin_books.php" class="nav-link active">
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
        <a href="admin_books.php" class="btn btn-secondary mb-4">⬅️ Quay lại</a>
        
        <h1 class="mb-4">Sửa Sách</h1>

        <?= $message ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Sách</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên sách</label>
                        <input type="text" name="book_name" class="form-control" value="<?= htmlspecialchars($book['book_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tác giả</label>
                        <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giá</label>
                        <input type="number" name="price" class="form-control" step="0.01" value="<?= $book['price'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Danh mục</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $book['category_id'] ? 'selected' : '' ?>>
                                    <?= $cat['category_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đường dẫn hình ảnh</label>
                        <input type="text" name="image_path" class="form-control" value="<?= htmlspecialchars($book['image_path']) ?>" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="update_book" class="btn btn-primary"><i class="bi bi-save"></i> Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
