<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Bạn cần đăng nhập để sử dụng giỏ hàng");
    exit;
}