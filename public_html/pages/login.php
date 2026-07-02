<?php
// 1. Khởi tạo Session ở dòng đầu tiên để lưu trạng thái đăng nhập
session_start();

$error_message = "";

// 2. Chỉ xử lý khi người dùng ấn nút ĐĂNG NHẬP (Gửi dữ liệu POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $dbname = 'worldofbook';
    $username_db = 'root';
    $password_db = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Lấy thông tin từ form
        $user_input = trim($_POST['username']);
        $password_input = $_POST['password'];

        // Kiểm tra xem dữ liệu nhập vào là Username hay Email (Sử dụng hàm LOWER để tránh lệch chữ hoa thường)
        $stmt = $conn->prepare("select * from users where lower(username) = lower(:user_input) or lower(email) = lower(:user_input)");
        $stmt->execute(['user_input' => $user_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu tìm thấy tài khoản trong Database
        if ($user) {
            // Xác thực mật khẩu đã mã hóa bằng hàm password_verify
            if (password_verify($password_input, $user['password_hash'])) {

                // Lưu dữ liệu quan trọng của người dùng vào Session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = isset($user['full_name']) ? $user['full_name'] : $user['username'];

                // Chuẩn hóa role về chữ thường để so sánh chính xác
                // Nếu role không tồn tại hoặc NULL, mặc định là 'user'
                $user_role = (!empty($user['role'])) ? strtolower(trim($user['role'])) : 'user';
                $_SESSION['role'] = $user_role;

                // Kiểm tra vai trò để điều hướng trang phù hợp
                if ($user_role === 'admin') {
                    header("Location: admin_orders.php");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error_message = "Mật khẩu không chính xác!";
            }
        } else {
            $error_message = "Tài khoản hoặc Email không tồn tại trên hệ thống!";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - World of Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container login-container">
        <div class="card login-card border-0 bg-white p-4">
            <div class="text-center mb-4">
                <a href="index.php" class="text-decoration-none text-dark">
                    <h3 class="fw-bold text-uppercase tracking-wider m-0">World of Book</h3>
                    <small class="text-muted">Hệ thống đăng nhập thành viên</small>
                </a>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger small p-2 text-center" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form id="mainLoginForm" method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Tên tài khoản hoặc Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" class="form-control bg-light border-start-0"
                            id="loginUsername" placeholder="Nhập tên tài khoản hoặc email" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i
                                class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0"
                            id="loginPassword" placeholder="Nhập mật khẩu của bạn" required>
                    </div>
                    <div class="text-end mt-1">
                        <a href="#" class="text-decoration-none small text-muted">Quên mật khẩu?</a>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold py-2.5 mb-3 btn-custom">Đăng Nhập</button>

                <p class="text-center small text-muted mb-0">
                    Chưa có tài khoản? <a href="register.php" class="fw-bold text-decoration-none text-primary">Đăng ký
                        ngay</a>
                </p>
            </form>
        </div>
    </div>

</body>

</html>