<?php
session_start();

// Nhúng file cấu hình kết nối database
require_once __DIR__ . '/pages/config.php';

// 3. Lấy danh sách Thể loại sách để đổ vào menu
$stmt_categories = $pdo->prepare("select * from categories");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// 4. Lấy danh sách Toàn bộ Sách kết hợp liên kết danh mục slug
$query_books = "select books.*, categories.category_slug 
                from books 
                left join categories on books.category_id = categories.category_id";
$stmt_books = $pdo->prepare($query_books);
$stmt_books->execute();
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

$cartCount = 0;
$cartPreview = [];
if (isset($_SESSION['user_id'])) {
    $stmt_cart = $pdo->prepare("select c.quantity, b.book_name, b.price, b.image_path from carts c join books b on c.book_id = b.book_id where c.user_id = ?");
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
    <link rel="stylesheet" href="pages/styles.css">

    <style>
        /* --- 1. Code sửa lỗi Flash Sale --- */
        @keyframes flash-animation {
            0% {
                opacity: 0.4;
                transform: scale(0.95);
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }

            100% {
                opacity: 0.4;
                transform: scale(0.95);
            }
        }

        .animation-flash {
            animation: flash-animation 1.2s infinite ease-in-out;
        }

        .flash-sale-scroll {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 1rem;
            padding-bottom: 10px;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        .flash-sale-card {
            width: 190px;
            flex-shrink: 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .flash-sale-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }

        /* --- 2. Sửa lỗi mất màu giảm giá & ảnh không đều --- */
        .badge-promo {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 4px;
            z-index: 5;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .custom-card-img {
            height: 240px;
            width: 100%;
            object-fit: cover;
            object-position: center;
            background-color: #f8f9fa;
        }

        .book-item .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .book-item .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }

        /* --- LÀM ĐẸP THANH FILTER THỂ LOẠI --- */
        .bg-light.p-3.rounded-3.shadow-sm.mb-4 {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef;
            padding: 1.25rem !important;
        }

        .d-inline-flex.gap-2 {
            gap: 0.5rem 0.75rem !important;
        }

        .btn-filter {
            padding: 6px 16px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border-radius: 20px !important;
            border: 1px solid #dee2e6 !important;
            background-color: #f8f9fa !important;
            color: #495057 !important;
            transition: all 0.2s ease-in-out !important;
        }

        .btn-filter:hover {
            background-color: #e9ecef !important;
            color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .btn-filter.active {
            background-color: #0d6efd !important;
            color: #ffffff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2) !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <button class="btn btn-dark text-white me-2" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#menuBaGach">
                <i class="bi bi-list fs-3"></i>
            </button>

            <a class="navbar-brand fw-bold text-uppercase tracking-wider" href="index.php">
                <img src="images/logo.jpg" alt="Logo" width="35" height="35" class="rounded-circle me-2"> World of Books
            </a>

            <form class="d-none d-md-flex mx-auto" style="width: 40%;" onsubmit="return false;">
                <div class="input-group">
                    <input class="form-control" type="search" id="searchDesktop"
                        placeholder="Tìm kiếm tựa sách, tác giả...">
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
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cartCount ?></span>
                    </a>

                    <div class="cart-dropdown shadow rounded border">
                        <div class="cart-arrow"></div>
                        <?php if ($cartCount > 0): ?>
                            <div class="p-3">
                                <h6 class="fw-bold mb-3">Giỏ hàng (<?= $cartCount ?>)</h6>
                                <?php foreach ($cartPreview as $item): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" class="me-3"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small mb-1"><?= htmlspecialchars($item['book_name']) ?></div>
                                            <div class="small text-muted">x<?= $item['quantity'] ?> •
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="d-grid">
                                    <a href="pages/cart.php" class="btn btn-primary btn-sm">Xem giỏ hàng</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center">
                                <img src="images/emptycart.jpg" alt="Chưa có sản phẩm" class="img-fluid mb-3"
                                    style="width: 80px; opacity: 0.6; filter: grayscale(30%);">
                                <p class="text-muted small mb-0 fw-bold">Chưa có sản phẩm</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuBaGach" aria-labelledby="menuBaGachLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title fw-bold" id="menuBaGachLabel"><i class="bi bi-shop me-2"></i>DANH MỤC</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
            <div class="p-3 d-block d-md-none bg-light">
                <form class="d-flex" onsubmit="return false;">
                    <input class="form-control me-2" type="search" id="searchMobile" placeholder="Tìm sách, tác giả...">
                    <button class="btn btn-primary" type="button">Tìm</button>
                </form>
            </div>

            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action py-3 fw-bold category-btn" data-filter="all"
                    data-bs-dismiss="offcanvas">
                    <i class="bi bi-house-door me-2"></i>Trang chủ
                </a>

                <button
                    class="list-group-item list-group-item-action py-3 fw-bold d-flex justify-content-between align-items-center"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseTheLoai" aria-expanded="true">
                    <span><i class="bi bi-bookmarks me-2"></i>THỂ LOẠI SÁCH</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <div class="collapse show bg-light" id="collapseTheLoai">
                    <div class="list-group list-group-flush ps-3">
                        <?php foreach ($categories as $cat): ?>
                            <a href="#" class="list-group-item list-group-item-action bg-transparent py-2 category-btn"
                                data-filter="<?= htmlspecialchars($cat['category_slug']) ?>"
                                data-bs-dismiss="offcanvas"><?= htmlspecialchars($cat['category_name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button
                    class="list-group-item list-group-item-action py-3 fw-bold d-flex justify-content-between align-items-center"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseHoTro" aria-expanded="false">
                    <span><i class="bi bi-headset me-2"></i>HỖ TRỢ KHÁCH HÀNG</span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <div class="collapse bg-light" id="collapseHoTro">
                    <div class="list-group list-group-flush ps-3">
                        <a href="pages/contact.php"
                            class="list-group-item list-group-item-action bg-transparent py-2"><i
                                class="bi bi-telephone text-primary me-2"></i>Liên hệ hỗ trợ</a>
                        <a href="pages/introduce.php"
                            class="list-group-item list-group-item-action bg-transparent py-2"><i
                                class="bi bi-info-circle text-success me-2"></i>Giới thiệu điều khoản</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row g-3">
            <div class="col-lg-8 col-12">
                <div id="heroCarousel" class="carousel slide shadow-sm rounded-3 overflow-hidden"
                    data-bs-ride="carousel" style="height: 320px;">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0"
                            class="active"></button>
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
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide-prev>
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide-next>
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

    <div class="container my-4">
        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-fill text-warning fs-4 animation-flash"></i>
                    <span class="fw-bold fs-5 m-0 text-uppercase">FLASH SALE</span>
                    <div class="d-flex gap-1 align-items-center ms-2">
                        <span class="badge bg-dark" id="hours">01</span>
                        <b class="text-white">:</b>
                        <span class="badge bg-dark" id="minutes">45</span>
                        <b class="text-white">:</b>
                        <span class="badge bg-dark" id="seconds">07</span>
                    </div>
                </div>
                <a href="#" class="text-white text-decoration-none small fw-bold">Xem Tất Cả <i
                        class="bi bi-chevron-right"></i></a>
            </div>

            <div class="card-body p-3">
                <div class="flash-sale-scroll">
                    <?php
                    $flash_sales = array_filter($books, function ($b) {
                        return isset($b['discount_percent']) && $b['discount_percent'] > 0;
                    });

                    if (count($flash_sales) > 0):
                        foreach ($flash_sales as $book):
                            $goc = $book['price'];
                            $giam = $book['discount_percent'];
                            $gia_ban = $goc - ($goc * $giam / 100);
                            $percent_sold = rand(40, 90);
                            ?>
                            <div class="card flash-sale-card shadow-sm border position-relative">
                                <span class="position-absolute top-0 start-0 badge bg-warning text-dark fw-bold m-2"
                                    style="z-index:3;">
                                    -<?= $giam ?>%
                                </span>

                                <a href="#" class="p-2 d-block text-center" data-bs-toggle="modal"
                                    data-bs-target="#bookDetailModal" data-id="<?= $book['book_id'] ?>"
                                    data-name="<?= htmlspecialchars($book['book_name']) ?>"
                                    data-author="<?= htmlspecialchars($book['author']) ?>"
                                    data-price="<?= number_format($gia_ban, 0, ',', '.') ?>đ"
                                    data-image="<?= htmlspecialchars($book['image_path']) ?>">
                                    <img src="<?= htmlspecialchars($book['image_path']) ?>" class="rounded object-fit-cover"
                                        style="height: 150px; width: 100%;" alt="<?= htmlspecialchars($book['book_name']) ?>">
                                </a>

                                <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                    <div class="text-truncate fw-bold small text-dark mb-1">
                                        <?= htmlspecialchars($book['book_name']) ?>
                                    </div>
                                    <div>
                                        <div class="text-danger fw-bold fs-5 mb-2">
                                            <?= number_format($gia_ban, 0, ',', '.') ?><span class="fs-6">đ</span>
                                        </div>
                                        <div class="progress position-relative" style="height: 16px; border-radius: 10px;">
                                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: <?= $percent_sold ?>%;"></div>
                                            <small class="position-absolute w-100 start-0 text-center fw-bold text-white"
                                                style="font-size: 9px; line-height: 16px; z-index: 2; text-shadow: 1px 1px 2px rgba(0,0,0,0.6);">SELLING
                                                FAST</small>
                                        </div>
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
    </div>

    <div class="container my-5">
        <h3 class="text-center fw-bold mb-4" id="sectionTitle">Sách nổi bật</h3>

        <div class="bg-light p-3 rounded-3 shadow-sm mb-4">
            <div class="text-center">
                <div class="d-inline-flex gap-2 flex-wrap justify-content-center">
                    <button class="btn btn-filter active" data-target="all">Tất cả sách</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="btn btn-filter"
                            data-target="<?= htmlspecialchars($cat['category_slug']) ?>"><?= htmlspecialchars($cat['category_name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

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

                            <img src="<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top custom-card-img"
                                alt="<?= htmlspecialchars($book['book_name']) ?>">

                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fs-6 fw-bold mb-1 book-name text-truncate-2">
                                        <?= htmlspecialchars($book['book_name']) ?>
                                    </h5>
                                    <p class="text-muted small mb-2 book-author"><?= htmlspecialchars($book['author']) ?></p>
                                    <input type="hidden" class="book-id" value="<?= $book['book_id'] ?>">
                                </div>
                                <div>
                                    <p class="card-text text-danger fw-bold mb-3">
                                        <?php if ($isPromo): ?>
                                            <span
                                                class="old-price text-decoration-line-through text-muted fs-6 me-1 fw-normal"><?= number_format($goc, 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                        <?= number_format($gia_ban, 0, ',', '.') ?>đ
                                    </p>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-50 fw-bold"
                                            data-bs-toggle="modal" data-bs-target="#bookDetailModal"
                                            data-id="<?= $book['book_id'] ?>"
                                            data-name="<?= htmlspecialchars($book['book_name']) ?>"
                                            data-author="<?= htmlspecialchars($book['author']) ?>"
                                            data-price="<?= number_format($gia_ban, 0, ',', '.') ?>đ"
                                            data-image="<?= htmlspecialchars($book['image_path']) ?>">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </button>

                                        <form method="POST" action="pages/add_to_cart.php" class="w-50">
                                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i
                                                    class="bi bi-cart-plus"></i> Thêm</button>
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
    </div>

    <div class="modal fade" id="bookDetailModal" tabindex="-1" aria-labelledby="bookDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="bookDetailModalLabel"><i
                            class="bi bi-info-circle me-2"></i>Thông tin chi tiết sách</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-5 text-center">
                            <img id="modalBookImage" src="" alt="" class="img-fluid rounded shadow-sm"
                                style="max-height: 320px; object-fit: cover;">
                        </div>
                        <div class="col-md-7 d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="fw-bold text-dark mb-2" id="modalBookName">Tên sách</h3>
                                <p class="text-muted mb-3 fs-5"><strong>Tác giả:</strong> <span
                                        id="modalBookAuthor">...</span></p>
                                <hr>
                                <h4 class="text-danger fw-bold mb-4" id="modalBookPrice">0đ</h4>
                                <p class="text-secondary small"><i class="bi bi-shield-check text-success me-1"></i> Sản
                                    phẩm được phân phối chính hãng trực tuyến bởi hệ thống cửa hàng <strong>World of
                                        Books</strong>.</p>
                            </div>
                            <div class="modal-footer border-0 p-0 mt-3 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary px-4"
                                    data-bs-dismiss="modal">Đóng</button>
                                <form method="POST" action="pages/add_to_cart.php" id="modalAddToCartForm">
                                    <input type="hidden" name="book_id" id="modalBookId" value="">
                                    <button type="submit" class="btn btn-primary px-4"><i
                                            class="bi bi-cart-plus me-1"></i> Thêm vào giỏ hàng</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            // 2. Logic JS: Đồng bộ hiệu ứng ACTIVE cho CSS thuốc nhộng
            const filterButtons = document.querySelectorAll('.btn-filter');
            const sidebarButtons = document.querySelectorAll('.category-btn');
            const bookItems = document.querySelectorAll('.book-item');
            const sectionTitle = document.getElementById('sectionTitle');

            function handleFiltering(filterValue, currentText) {
                // Cập nhật tiêu đề h3 động dựa trên giá trị lựa chọn
                if (filterValue === 'all') {
                    sectionTitle.textContent = "Sách nổi bật";
                } else {
                    sectionTitle.textContent = "Thể loại: " + currentText;
                }

                // Đồng bộ trạng thái active cho nhóm nút bấm ở thanh trung tâm
                filterButtons.forEach(b => {
                    const target = b.getAttribute('data-target');
                    if (target === filterValue) {
                        b.classList.add('active');
                    } else {
                        b.classList.remove('active');
                    }
                });

                // Lọc ẩn/hiện danh sách Grid các đầu sách
                bookItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    if (filterValue === 'all' || itemCategory === filterValue) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            // Bắt sự kiện khi click các nút hình viên thuốc ở giữa trang
            filterButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const filterValue = this.getAttribute('data-target');
                    handleFiltering(filterValue, this.textContent.trim());
                });
            });

            // Bắt sự kiện khi chọn danh mục từ Menu ba gạch (Sidebar)
            sidebarButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const filterValue = this.getAttribute('data-filter');
                    handleFiltering(filterValue, this.textContent.trim());
                });
            });
            // --- LOGIC TÌM KIẾM SÁCH THEO THỜI GIAN THỰC (REAL-TIME SEARCH) ---
const searchDesktop = document.getElementById('searchDesktop');
const searchMobile = document.getElementById('searchMobile');

function filterBooksByKeyword(keyword) {
    const term = keyword.toLowerCase().trim();
    const bookItems = document.querySelectorAll('.book-item');

    bookItems.forEach(item => {
        // Lấy tên sách và tên tác giả trong card
        const title = item.querySelector('.book-name') ? item.querySelector('.book-name').textContent.toLowerCase() : '';
        const author = item.querySelector('.book-author') ? item.querySelector('.book-author').textContent.toLowerCase() : '';

        // Nếu từ khóa xuất hiện trong Tên sách hoặc Tác giả -> Hiển thị
        if (title.includes(term) || author.includes(term)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Lắng nghe sự kiện gõ phím ở ô tìm kiếm Desktop
if (searchDesktop) {
    searchDesktop.addEventListener('input', function () {
        filterBooksByKeyword(this.value);
        if (searchMobile) searchMobile.value = this.value; // Đồng bộ với ô mobile
    });
}

// Lắng nghe sự kiện gõ phím ở ô tìm kiếm Mobile
if (searchMobile) {
    searchMobile.addEventListener('input', function () {
        filterBooksByKeyword(this.value);
        if (searchDesktop) searchDesktop.value = this.value; // Đồng bộ với ô desktop
    });
}
        });
    </script>
</body>

</html>