<?php
// 1. Khởi động Session đầu tiên để quản lý trạng thái đăng nhập của thành viên
session_start();

// 2. Cấu hình kết nối Database bằng PDO - Sử dụng chính xác dbname của bạn là 'worldofbook'
$host = 'localhost';
$dbname = 'worldofbook'; 
$username = 'root'; // Mặc định của XAMPP
$password = '';     // Mặc định của XAMPP trống

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối Database thất bại: " . $e->getMessage());
}

// 3. Lấy danh sách Thể loại sách để đổ vào menu
$stmt_categories = $conn->prepare("SELECT * FROM Categories");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// 4. Lấy danh sách Toàn bộ Sách kết hợp liên kết danh mục slug
$query_books = "SELECT Books.*, Categories.category_slug 
                FROM Books 
                LEFT JOIN Categories ON Books.category_id = Categories.category_id";
$stmt_books = $conn->prepare($query_books);
$stmt_books->execute();
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World of Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="pages/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <button class="btn btn-dark text-white me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuBaGach">
                <i class="bi bi-list fs-3"></i>
            </button>

            <a class="navbar-brand fw-bold text-uppercase tracking-wider" href="index.php">
                <img src="images/logo.jpg" alt="Logo" width="35" height="35" class="rounded-circle me-2"> World of Books
            </a>
            
            <form class="d-none d-md-flex mx-auto" style="width: 40%;" onsubmit="return false;">
                <div class="input-group">
                    <input class="form-control" type="search" id="searchDesktop" placeholder="Tìm kiếm tựa sách, tác giả...">
                    <button class="btn btn-primary" type="button"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
                    <span class="text-white me-1 small">
                        <i class="bi bi-person-check-fill text-success me-1"></i> 
                        Chào, <span class="text-warning fw-bold"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                    </span>
                    <a href="pages/logout.php" class="btn btn-danger btn-sm fw-bold me-1">
                        <i class="bi bi-box-arrow-right me-1"></i> Thoát
                    </a>
                <?php else: ?>
                    <a href="pages/login.php" class="btn btn-outline-light btn-sm fw-bold me-1">
                        <i class="bi bi-person-circle me-1"></i> Đăng nhập
                    </a>
                <?php endif; ?>

                <div class="position-relative cart-wrapper">
                    <a href="#" class="btn btn-outline-light">
                        <i class="bi bi-cart3"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                    
                    <div class="cart-dropdown shadow rounded border">
                        <div class="cart-arrow"></div>
                        <div class="p-4 text-center">
                            <img src="images/emptycart.jpg" alt="Chưa có sản phẩm" class="img-fluid mb-3" style="width: 80px; opacity: 0.6; filter: grayscale(30%);">
                            <p class="text-muted small mb-0 fw-bold">Chưa có sản phẩm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuBaGach" aria-labelledby="menuBaGachLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title fw-bold" id="menuBaGachLabel"><i class="bi bi-shop me-2"></i>DANH MỤC</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body p-0">
            <div class="p-3 d-block d-md-none bg-light">
                <form class="d-flex" onsubmit="return false;">
                    <input class="form-control me-2" type="search" id="searchMobile" placeholder="Tìm sách, tác giả...">
                    <button class="btn btn-primary" type="button">Tìm</button>
                </form>
            </div>

            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action py-3 fw-bold category-btn" data-filter="all"><i class="bi bi-house-door me-2"></i>Trang chủ / Tất cả</a>
                <a href="#" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-fire me-2"></i>Sách bán chạy</a>
                <a href="#" class="list-group-item list-group-item-action py-3 fw-bold"><i class="bi bi-gift me-2"></i>Chương trình khuyến mãi</a>
                
                <button class="list-group-item list-group-item-action py-3 fw-bold d-flex justify-content-between align-items-center" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseTheLoai" aria-expanded="true">
                    <span><i class="bi bi-bookmarks me-2"></i>THỂ LOẠI SÁCH</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <div class="collapse show bg-light" id="collapseTheLoai">
                    <div class="list-group list-group-flush ps-3">
                        <?php foreach ($categories as $cat): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-transparent py-2 category-btn" data-filter="<?= htmlspecialchars($cat['category_slug']) ?>"><?= htmlspecialchars($cat['category_name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button class="list-group-item list-group-item-action py-3 fw-bold d-flex justify-content-between align-items-center" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseHoTro" aria-expanded="false">
                    <span><i class="bi bi-headset me-2"></i>HỖ TRỢ KHÁCH HÀNG</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <div class="collapse bg-light" id="collapseHoTro">
                    <div class="list-group list-group-flush ps-3">
                        <a href="#" class="list-group-item list-group-item-action bg-transparent py-2"><i class="bi bi-telephone me-2"></i>Liên hệ hỗ trợ</a>
                        <a href="#" class="list-group-item list-group-item-action bg-transparent py-2"><i class="bi bi-info-circle me-2"></i>Giới thiệu điều khoản</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row g-3">
            <div class="col-lg-8 col-12">
                <div id="heroCarousel" class="carousel slide shadow-sm rounded-3 overflow-hidden" data-bs-ride="carousel" style="height: 320px;">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                    </div>
                    <div class="carousel-inner h-100">
                        <div class="carousel-item active h-100">
                            <img src="images/banner1.jpg" class="w-100 h-100 object-fit-cover" alt="Banner Chính 1">
                        </div>
                        <div class="carousel-item h-100">
                            <img src="images/banner2.jpg" class="w-100 h-100 object-fit-cover" alt="Banner Chính 2">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide-prev>
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-flex flex-column justify-content-between" style="height: 320px;">
                <div class="sub-banner-wrapper shadow-sm rounded-3 overflow-hidden" style="height: calc(50% - 6px);">
                    <img src="images/sub1.jpg" class="w-100 h-100 object-fit-cover" alt="Ưu đãi phụ 1">
                </div>
                <div class="sub-banner-wrapper shadow-sm rounded-3 overflow-hidden" style="height: calc(50% - 6px);">
                    <img src="images/sub2.jpg" class="w-100 h-100 object-fit-cover" alt="Ưu đãi phụ 2">
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <h3 class="text-center fw-bold mb-4">Sách nổi bật</h3>
        
        <div class="d-flex justify-content-center flex-wrap gap-2 mb-4">
            <button class="btn btn-filter active" data-target="all">Tất cả</button>
            <?php foreach ($categories as $cat): ?>
                <button class="btn btn-filter" data-target="<?= htmlspecialchars($cat['category_slug']) ?>"><?= htmlspecialchars($cat['category_name']) ?></button>
            <?php endforeach; ?>
        </div>
            
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4" id="bookGrid">
            
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="col book-item" data-category="<?= htmlspecialchars($book['category_slug']) ?>">
                        <div class="card h-100 shadow-sm border-0 d-flex flex-column">
                            <img src="<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top custom-card-img" alt="<?= htmlspecialchars($book['book_name']) ?>">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fs-6 fw-bold mb-1 book-name"><?= htmlspecialchars($book['book_name']) ?></h5>
                                    <p class="text-muted small mb-2 book-author"><?= htmlspecialchars($book['author']) ?></p>
                                    <input type="hidden" class="book-id" value="<?= $book['book_id'] ?>">
                                </div>
                                <div>
                                    <p class="card-text text-danger fw-bold mb-3"><?= number_format($book['price'], 0, ',', '.') ?>đ</p>
                                    <button class="btn btn-primary w-100"><i class="bi bi-cart-plus me-1"></i> Thêm giỏ hàng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Không tìm thấy quyển sách nào trong Hệ thống Cơ sở dữ liệu.</p>
                </div>
            <?php endif; ?>

        </div>

        <div class="text-center mt-5">
            <button class="btn btn-outline-primary px-5 py-2 fw-bold shadow-sm">
                Xem thêm sản phẩm <i class="bi bi-arrow-down-short fs-5 align-middle"></i>
            </button>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2026 World of Books.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="pages/script.js"></script>
</body>
</html>