<?php
// check_admin.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem đã đăng nhập chưa HOẶC vai trò có phải là admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Nếu không phải admin, chuyển hướng về trang đăng nhập và báo lỗi
    header("Location: login.php?error=Bạn không có quyền truy cập!");
    exit;
}
?>