<?php
// Cấu hình kết nối Database bằng PDO
$host = 'localhost';
$dbname = 'worldofbook'; 
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối Database thất bại: " . $e->getMessage());
}
?>