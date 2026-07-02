<?php
require_once 'check_admin.php';
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_categories.php");
    exit;
}

$category_id = intval($_GET['id']);
$message = '';

// Lấy thông tin danh mục
$stmt = $pdo->prepare("select * from categories where category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: admin_categories.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_name = trim($_POST['category_name']);
    $category_slug = trim($_POST['category_slug']);

    if ($category_name && $category_slug) {
        $stmt = $pdo->prepare("update categories set category_name = ?, category_slug = ? where category_id = ?");
        if ($stmt->execute([$category_name, $category_slug, $category_id])) {
            $message = '<div class="alert alert-success">Cập nhật danh mục thành công!</div>';
            // Reload data
            $stmt = $pdo->prepare("select * from categories where category_id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch();
        } else {
            $message = '<div class="alert alert-danger">Lỗi: Slug đã tồn tại!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Danh mục - WorldOfBook</title>
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
            <a href="admin_orders.php" class="nav-link">
                <i class="bi bi-box-seam"></i> Quản lý đơn hàng
            </a>
            <a href="admin_books.php" class="nav-link">
                <i class="bi bi-book"></i> Quản lý sách
            </a>
            <a href="admin_categories.php" class="nav-link active">
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
        <a href="admin_categories.php" class="btn btn-secondary mb-4">⬅️ Quay lại</a>

        <h1 class="mb-4">Sửa Danh mục</h1>

        <?= $message ?>

        <div class="card" style="max-width: 500px;">
            <div class="card-header">
                <h5 class="mb-0">Thông tin Danh mục</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" name="category_name" class="form-control"
                            value="<?= htmlspecialchars($category['category_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (định danh URL)</label>
                        <input type="text" name="category_slug" class="form-control"
                            value="<?= htmlspecialchars($category['category_slug']) ?>" required>
                    </div>
                    <button type="submit" name="update_category" class="btn btn-primary"><i class="bi bi-save"></i> Cập
                        nhật</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>