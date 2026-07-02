<?php
// 1. Khởi tạo biến để lưu thông báo lỗi hoặc thành công nếu có
$error_message = "";
$success_message = "";

// 2. Chỉ xử lý dữ liệu khi người dùng bấm nút submit (Gửi dữ liệu dạng POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $dbname = 'worldofbook';
    $username = 'root';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy dữ liệu từ form và loại bỏ khoảng trắng thừa
        $user = trim($_POST['username']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];

        // Kiểm tra xem Username hoặc Email đã tồn tại trong Hệ thống chưa
        $stmt_check = $conn->prepare("select * from users where username = :username or email = :email");
        $stmt_check->execute(['username' => $user, 'email' => $email]);

        if ($stmt_check->rowCount() > 0) {
            $error_message = "Tên đăng nhập hoặc Email này đã được sử dụng!";
        } else {
            // Mã hóa mật khẩu bảo mật trước khi lưu vào Database
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            // Chèn dữ liệu tài khoản mới vào bảng Users
            $stmt_insert = $conn->prepare("insert into users (username, email, password_hash, full_name, role) values (:username, :email, :password_hash, :full_name, :role)");
            $stmt_insert->execute([
                'username' => $user,
                'email' => $email,
                'password_hash' => $hashed_password,
                'full_name' => $user,
                'role' => 'user'
            ]);

            $success_message = "Đăng ký thành công! Đang chuyển hướng sang trang đăng nhập...";
            header("refresh:2;url=login.php");
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi hệ thống (SQL): " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container register-container">
        <div class="card register-card border-0 bg-white p-4">
            <div class="text-center mb-4">
                <a href="index.php" class="text-decoration-none text-dark">
                    <h3 class="fw-bold text-uppercase tracking-wider m-0">World of Book</h3>
                    <small class="text-muted">Đăng ký thành viên mới</small>
                </a>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger small p-2 text-center" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success small p-2 text-center" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <form id="mainRegisterForm" method="POST" action="register.php">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0"
                            placeholder="Ít nhất 5 ký tự" minlength="5" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Địa chỉ Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control bg-light border-start-0"
                            placeholder="viethung@example.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0"
                            placeholder="Tối thiểu 8 ký tự" minlength="8" required>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="termsAgree" required>
                    <label class="form-check-label small text-muted" for="termsAgree">
                        Tôi đồng ý với các <a href="#" class="text-decoration-none">Điều khoản dịch vụ</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2.5 mb-3 btn-register">Đăng ký</button>

                <p class="text-center small text-muted mb-0">
                    Đã có tài khoản rồi? <a href="login.php" class="fw-bold text-decoration-none text-primary">Đăng nhập
                        ngay</a>
                </p>
            </form>
        </div>
    </div>

</body>

</html>