<?php
session_start();
session_unset();   // Xóa tất cả các biến Session đang lưu trữ
session_destroy(); // Hủy bỏ hoàn toàn Session hiện tại
header("Location: ../index.php"); // Tự động quay về trang chủ sau khi thoát
exit();
?>