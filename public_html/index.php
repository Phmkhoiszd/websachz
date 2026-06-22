<?php
session_start();

// 2. Cấu hình kết nối Database bằng PDO
$host = 'localhost';
$dbname = 'worldofbook'; 
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password); // Kết nối Database
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

$cartCount = 0;
$cartPreview = [];
if (isset($_SESSION['user_id'])) {
    $stmt_cart = $conn->prepare("SELECT c.quantity, b.book_name, b.price, b.image_path FROM carts c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = ?");
    $stmt_cart->execute([$_SESSION['user_id']]);
    $cartPreview = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);
    $cartCount = count($cartPreview);
}
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
    <style>
        .badge-promo {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            z-index: 2;
        }
        .old-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9em;
            margin-right: 5px;
        }
        /* Style sửa lỗi báo đỏ text-uppercase */
        .filter-group-title {
            font-size: 0.85rem;
            text-transform: uppercase; 
            letter-spacing: 1px;
            color: #6c757d;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* Khối Flash Sale Tổng kiểu Shopee */
        .flash-sale-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
        }
        .flash-sale-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .flash-sale-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff4742;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        /* Đồng hồ đếm ngược */
        .countdown-box {
            display: inline-flex;
            gap: 4px;
            margin-left: 10px;
        }
        .countdown-time {
            background-color: #000;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        /* Thanh cuộn ngang cho sản phẩm sale */
        .flash-sale-slider {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 10px;
        }
        .flash-sale-slider::-webkit-scrollbar {
            height: 6px;
        }
        .flash-sale-slider::-webkit-scrollbar-thumb {
            background-color: #e0e0e0;
            border-radius: 4px;
        }
        /* Card sản phẩm Flash Sale */
        .flash-sale-item {
            flex: 0 0 calc(16.666% - 13px);
            min-width: 160px;
            position: relative;
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        /* Nhãn giảm giá chéo góc góc phải giống Shopee */
        .shopee-badge-promo {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(255, 212, 36, 0.9);
            color: #ec3818;
            padding: 4px 6px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            border-bottom-left-radius: 4px;
            z-index: 3;
        }
        .shopee-badge-promo::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 0;
            border-left: 14px solid transparent;
            border-right: 14px solid transparent;
            border-top: 4px solid rgba(255, 212, 36, 0.9);
        }
        /* Giá và thanh tiến trình SELLING FAST */
        .flash-sale-price {
            color: #ee4d2d;
            font-size: 1.1rem;
            font-weight: 500;
            text-align: center;
            margin-top: 8px;
            margin-bottom: 4px;
        }
        .selling-fast-bar {
            position: relative;
            background-color: #ffbda6;
            color: #fff;
            border-radius: 10px;
            height: 16px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 1;
        }
        .selling-fast-progress {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: linear-gradient(90deg, #ff5722, #ff7337);
            z-index: -1;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
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
                    <a href="pages/cart.php" class="btn btn-outline-light position-relative">
                        <i class="bi bi-cart3"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cartCount ?></span>
                    </a>
                    
                    <div class="cart-dropdown shadow rounded border">
                        <div class="cart-arrow"></div>
                        <?php if ($cartCount > 0): ?>
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">Giỏ hàng (<?= $cartCount ?>)</h6>
                                <?php foreach ($cartPreview as $item): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small mb-1"><?= htmlspecialchars($item['book_name']) ?></div>
                                            <div class="small text-muted">x<?= $item['quantity'] ?> • <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="d-grid">
                                    <a href="pages/cart.php" class="btn btn-primary btn-sm">Xem giỏ hàng</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center">
                                <img src="images/emptycart.jpg" alt="Chưa có sản phẩm" class="img-fluid mb-3" style="width: 80px; opacity: 0.6; filter: grayscale(30%);">
                                <p class="text-muted small mb-0 fw-bold">Chưa có sản phẩm</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- OFFCANVAS MENU -->
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
                <a href="#" class="list-group-item list-group-item-action py-3 fw-bold category-btn" data-filter="all" data-bs-dismiss="offcanvas">
                    <i class="bi bi-house-door me-2"></i>Trang chủ
                </a>
                
                <button class="list-group-item list-group-item-action py-3 fw-bold d-flex justify-content-between align-items-center" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseTheLoai" aria-expanded="true">
                    <span><i class="bi bi-bookmarks me-2"></i>THỂ LOẠI SÁCH</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <div class="collapse show bg-light" id="collapseTheLoai">
                    <div class="list-group list-group-flush ps-3">
                        <?php foreach ($categories as $cat): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-transparent py-2 category-btn" data-filter="<?= htmlspecialchars($cat['category_slug']) ?>" data-bs-dismiss="offcanvas"><?= htmlspecialchars($cat['category_name']) ?></a>
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
                      <a href="pages/contact.php" class="list-group-item list-group-item-action bg-transparent py-2"><i class="bi bi-telephone text-primary me-2"></i>Liên hệ hỗ trợ</a>
<a href="pages/introduce.php" class="list-group-item list-group-item-action bg-transparent py-2"><i class="bi bi-info-circle text-success me-2"></i>Giới thiệu điều khoản</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HERO BANNER -->
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

    <!-- SECTION FLASH SALE (ĐÃ GIỮ LẠI THEO YÊU CẦU) -->
    <div class="container my-4">
        <div class="flash-sale-container shadow-sm border">
            <div class="flash-sale-header">
                <div class="flash-sale-title">
                    <i class="bi bi-lightning-fill text-danger fs-3"></i> FLASH SALE
                    <div class="countdown-box">
                        <span class="countdown-time" id="hours">01</span>
                        <span class="countdown-time" id="minutes">45</span>
                        <span class="countdown-time" id="seconds">07</span>
                    </div>
                </div>
                <a href="#" class="text-danger text-decoration-none small fw-bold">Xem Tất Cả <i class="bi bi-chevron-right"></i></a>
            </div>

            <div class="flash-sale-slider">
                <?php 
                // Lọc ra sách có discount_percent > 0 để đưa vào hàng Flash Sale trượt ngang
                $flash_sales = array_filter($books, function($b) {
                    return isset($b['discount_percent']) && $b['discount_percent'] > 0;
                });

                if (count($flash_sales) > 0):
                    foreach ($flash_sales as $book): 
                        $goc = $book['price'];
                        $giam = $book['discount_percent'];
                        $gia_ban = $goc - ($goc * $giam / 100);
                        $percent_sold = rand(40, 90); 
                ?>
                    <div class="flash-sale-item shadow-sm">
                        <div class="shopee-badge-promo">
                            <div><i class="bi bi-lightning-fill"></i></div>
                            <div>-<?= $giam ?>%</div>
                        </div>
                        
                        <a href="#" data-bs-toggle="modal" data-bs-target="#bookDetailModal"
                           data-id="<?= $book['book_id'] ?>"
                           data-name="<?= htmlspecialchars($book['book_name']) ?>"
                           data-author="<?= htmlspecialchars($book['author']) ?>"
                           data-price="<?= number_format($gia_ban, 0, ',', '.') ?>đ"
                           data-image="<?= htmlspecialchars($book['image_path']) ?>">
                            <img src="<?= htmlspecialchars($book['image_path']) ?>" class="w-100 object-fit-cover mb-2" style="height: 150px; border-radius: 4px;" alt="<?= htmlspecialchars($book['book_name']) ?>">
                        </a>

                        <div>
                            <div class="flash-sale-price">
                                <?= number_format($gia_ban, 0, ',', '.') ?><span class="fs-6">đ</span>
                            </div>
                            <div class="selling-fast-bar">
                                <span class="position-relative" style="z-index: 2;">SELLING FAST</span>
                                <div class="selling-fast-progress" style="width: <?= $percent_sold ?>%;"></div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach; 
                else:
                ?>
                    <div class="text-center w-100 py-4 text-muted">Hiện tại chưa có chương trình Flash Sale nào.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KHU VỰC BỘ LỌC THỂ LOẠI VÀ GRID SÁCH -->
    <div class="container my-5">
        <h3 class="text-center fw-bold mb-4" id="sectionTitle">Sách nổi bật</h3>
        
        <!-- CHỈ GIỮ LẠI LỌC THEO THỂ LOẠI -->
        <div class="bg-light p-3 rounded-3 shadow-sm mb-4">
            <div class="text-center">
                <div class="filter-group-title"><i class="bi bi-bookmarks me-1"></i> Phân loại theo Thể loại</div>
                <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                    <button class="btn btn-sm btn-dark btn-filter active" data-target="all">Tất cả sách</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-sm btn-outline-secondary btn-filter" data-target="<?= htmlspecialchars($cat['category_slug']) ?>"><?= htmlspecialchars($cat['category_name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
            
        <!-- GRID SÁCH -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4" id="bookGrid">
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): 
                    $isPromo = isset($book['discount_percent']) && $book['discount_percent'] > 0;
                    $goc = $book['price'];
                    $giam_gia = $isPromo ? $book['discount_percent'] : 0;
                    $gia_ban = $goc - ($goc * $giam_gia / 100);
                ?>
                    <div class="col book-item" data-category="<?= htmlspecialchars($book['category_slug']) ?>">
                        <div class="card h-100 shadow-sm border-0 d-flex flex-column position-relative">
                            <?php if ($isPromo): ?>
                                <div class="badge-promo">-<?= $giam_gia ?>%</div>
                            <?php endif; ?>

                            <img src="<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top custom-card-img" alt="<?= htmlspecialchars($book['book_name']) ?>">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fs-6 fw-bold mb-1 book-name"><?= htmlspecialchars($book['book_name']) ?></h5>
                                    <p class="text-muted small mb-2 book-author"><?= htmlspecialchars($book['author']) ?></p>
                                    <input type="hidden" class="book-id" value="<?= $book['book_id'] ?>">
                                </div>
                                <div>
                                    <p class="card-text text-danger fw-bold mb-3">
                                        <?php if ($isPromo): ?>
                                            <span class="old-price"><?= number_format($goc, 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                        <?= number_format($gia_ban, 0, ',', '.') ?>đ
                                    </p>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-50 fw-bold" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#bookDetailModal"
                                                data-id="<?= $book['book_id'] ?>"
                                                data-name="<?= htmlspecialchars($book['book_name']) ?>"
                                                data-author="<?= htmlspecialchars($book['author']) ?>"
                                                data-price="<?= number_format($gia_ban, 0, ',', '.') ?>đ"
                                                data-image="<?= htmlspecialchars($book['image_path']) ?>">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </button>
                                        
                                        <form method="POST" action="pages/add_to_cart.php" class="w-50">
                                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-cart-plus"></i> Thêm</button>
                                        </form>
                                    </div>
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

    <!-- MODAL CHI TIẾT SẢN PHẨM -->
    <div class="modal fade" id="bookDetailModal" tabindex="-1" aria-labelledby="bookDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="bookDetailModalLabel"><i class="bi bi-info-circle me-2"></i>Thông tin chi tiết sách</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-5 text-center">
                            <img id="modalBookImage" src="" alt="" class="img-fluid rounded shadow-sm" style="max-height: 320px; object-fit: cover;">
                        </div>
                        <div class="col-md-7 d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="fw-bold text-dark mb-2" id="modalBookName">Tên sách</h3>
                                <p class="text-muted mb-3 fs-5"><strong>Tác giả:</strong> <span id="modalBookAuthor">...</span></p>
                                <hr>
                                <h4 class="text-danger fw-bold mb-4" id="modalBookPrice">0đ</h4>
                                <p class="text-secondary small"><i class="bi bi-shield-check text-success me-1"></i> Sản phẩm được phân phối chính hãng trực tuyến bởi hệ thống cửa hàng <strong>World of Books</strong>.</p>
                            </div>
                            <div class="modal-footer border-0 p-0 mt-3 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                                <form method="POST" action="pages/add_to_cart.php" id="modalAddToCartForm">
                                    <input type="hidden" name="book_id" id="modalBookId" value="">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; World of Books.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="pages/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Đồng hồ đếm ngược số chạy lùi thời gian thực tế cho Flash Sale
        function startCountdown(durationInSeconds) {
            let timer = durationInSeconds;
            const hoursElem = document.getElementById('hours');
            const minutesElem = document.getElementById('minutes');
            const secondsElem = document.getElementById('seconds');

            setInterval(function () {
                let hours = parseInt(timer / 3600, 10);
                let minutes = parseInt((timer % 3600) / 60, 10);
                let seconds = parseInt(timer % 60, 10);

                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                if (hoursElem) hoursElem.textContent = hours;
                if (minutesElem) minutesElem.textContent = minutes;
                if (secondsElem) secondsElem.textContent = seconds;

                if (--timer < 0) {
                    timer = durationInSeconds; 
                }
            }, 1000);
        }
        startCountdown(6307);

        // 1. Xử lý dữ liệu hiển thị trên Modal
        const bookDetailModal = document.getElementById('bookDetailModal');
        if (bookDetailModal) {
            bookDetailModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const author = button.getAttribute('data-author');
                const price = button.getAttribute('data-price');
                const image = button.getAttribute('data-image');

                document.getElementById('modalBookName').textContent = name;
                document.getElementById('modalBookAuthor').textContent = author;
                document.getElementById('modalBookPrice').textContent = price;
                document.getElementById('modalBookImage').src = image;
                document.getElementById('modalBookImage').alt = name;
                document.getElementById('modalBookId').value = id;
            });
        }

        // 2. Logic JS xử lý Filter theo Category_slug
        const filterButtons = document.querySelectorAll('.btn-filter, .category-btn');
        const bookItems = document.querySelectorAll('.book-item');
        const sectionTitle = document.getElementById('sectionTitle');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                if(this.tagName === 'A') e.preventDefault();

                const filterValue = this.getAttribute('data-target') || this.getAttribute('data-filter');

                if(filterValue === 'all') {
                    sectionTitle.textContent = "Sách nổi bật";
                } else {
                    sectionTitle.textContent = "Thể loại: " + this.textContent.trim();
                }

                document.querySelectorAll('.btn-filter').forEach(b => {
                    b.classList.remove('active', 'btn-dark', 'btn-secondary');
                    const target = b.getAttribute('data-target');
                    if (target === 'all') {
                        b.classList.add('btn-outline-dark');
                    } else {
                        b.classList.add('btn-outline-secondary');
                    }
                });

                const matchedCenterBtns = document.querySelectorAll(`.btn-filter[data-target="${filterValue}"]`);
                matchedCenterBtns.forEach(matchedBtn => {
                    matchedBtn.classList.remove('btn-outline-dark', 'btn-outline-secondary');
                    matchedBtn.classList.add('active');
                    if (filterValue === 'all') {
                        matchedBtn.classList.add('btn-dark');
                    } else {
                        matchedBtn.classList.add('btn-secondary');
                    }
                });

                bookItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    if (filterValue === 'all' || itemCategory === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
</body>
</html>