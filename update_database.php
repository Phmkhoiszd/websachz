<?php
// update_database.php - Script cập nhật database để thêm cột role và tài khoản admin

$host = 'localhost';
$dbname = 'worldofbook';
$username_db = 'root';
$password_db = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Thêm cột role nếu chưa có
    $checkColumn = $conn->query("show columns from users like 'role'");
    if ($checkColumn->rowCount() == 0) {
        $conn->exec("alter table users add column role varchar(50) default 'user' after full_name");
        echo "✓ Đã thêm cột 'role' vào bảng users<br>";
    } else {
        echo "ℹ Cột 'role' đã tồn tại<br>";
    }

    // 2. Tạo tài khoản admin mặc định (nếu chưa tồn tại)
    $stmt = $conn->prepare("select * from users where username = 'admin'");
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Mật khẩu mặc định: admin123
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt_insert = $conn->prepare("insert into users (username, email, password_hash, full_name, role) values (:username, :email, :password_hash, :full_name, :role)");
        $stmt_insert->execute([
            'username' => 'admin',
            'email' => 'admin@worldofbook.com',
            'password_hash' => $password_hash,
            'full_name' => 'Administrator',
            'role' => 'admin'
        ]);
        echo "✓ Đã tạo tài khoản admin<br>";
        echo "  Username: <strong>admin</strong><br>";
        echo "  Password: <strong>admin123</strong><br>";
        echo "  ⚠ Hãy đổi mật khẩu sau khi đăng nhập!<br>";
    } else {
        echo "ℹ Tài khoản 'admin' đã tồn tại<br>";
    }

    echo "<br><strong style='color: green;'>✓ Cập nhật database thành công!</strong>";

} catch (PDOException $e) {
    echo "<strong style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</strong>";
}
?>