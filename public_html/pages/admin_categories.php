<?php
require_once 'check_admin.php';
require_once 'db.php';

$message = '';

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $category_slug = trim($_POST['category_slug']);

    if ($category_name && $category_slug) {
        $stmt = $pdo->prepare("insert into categories (category_name, category_slug) values (?, ?)");
        if ($stmt->execute([$category_name, $category_slug])) {
            $message = '<div class="alert alert-success">Thêm danh mục thành công!</div>';
        } else {
            $message = '<div class="alert alert-danger">Lỗi: Slug đã tồn tại hoặc có lỗi khác!</div>';
        }
    }
}

// Xử lý xóa danh mục
if (isset($_GET['delete'])) {
    $category_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("delete from categories where category_id = ?");
    if ($stmt->execute([$category_id])) {
        $message = '<div class="alert alert-success">Xóa danh mục thành công!</div>';
    }
}

// Lấy danh sách danh mục
$stmt = $pdo->query("select * from categories");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục - WorldOfBook</title>
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
        <h1 class="mb-4"><i class="bi bi-list-ul"></i> Quản lý Danh mục</h1>

        <?= $message ?>

        <!-- Add Category Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thêm Danh mục Mới</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug (định danh URL)</label>
                        <input type="text" name="category_slug" class="form-control" placeholder="e.g., van-hoc"
                            required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_category" class="btn btn-primary"><i class="bi bi-plus"></i>
                            Thêm danh mục</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách Danh mục (<?= count($categories) ?> mục)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th>Slug</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $cat['category_id'] ?></td>
                                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                    <td><code><?= htmlspecialchars($cat['category_slug']) ?></code></td>
                                    <td>
                                        <a href="admin_edit_category.php?id=<?= $cat['category_id'] ?>"
                                            class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="?delete=<?= $cat['category_id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>