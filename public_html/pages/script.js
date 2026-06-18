document.addEventListener("DOMContentLoaded", function () {
    // 1. LẤY CÁC PHẦN TỬ GIAO DIỆN
    const searchDesktop = document.getElementById("searchDesktop");
    const searchMobile = document.getElementById("searchMobile");
    const filterButtons = document.querySelectorAll(".btn-filter"); // Nút ngoài trang chủ
    const categoryButtons = document.querySelectorAll(".category-btn"); // Nút trong menu trái
    const bookItems = document.querySelectorAll(".book-item");

    // Biến lưu trạng thái lọc hiện tại (mặc định là 'all')
    let currentCategory = "all";

    // 2. HÀM XỬ LÝ LỌC VÀ TÌM KIẾM TỔNG HỢP
    function filterBooks() {
        // Lấy từ khóa tìm kiếm (ưu tiên ô đang được gõ, chuyển về chữ thường, bỏ khoảng trắng thừa)
        const keyword = (searchDesktop.value || searchMobile.value || "").toLowerCase().trim();

        bookItems.forEach((item) => {
            const bookName = item.querySelector(".book-name").textContent.toLowerCase();
            const bookAuthor = item.querySelector(".book-author").textContent.toLowerCase();
            const bookCategory = item.getAttribute("data-category");

            // Điều kiện 1: Khớp thể loại (Nếu là 'all' thì luôn đúng, ngược lại phải trùng mã thể loại)
            const matchesCategory = (currentCategory === "all" || bookCategory === currentCategory);

            // Điều kiện 2: Khớp từ khóa tìm kiếm (Tên sách hoặc Tên tác giả chứa từ khóa)
            const matchesKeyword = (bookName.includes(keyword) || bookAuthor.includes(keyword));

            // Nếu thỏa mãn cả 2 điều kiện thì hiển thị, ngược lại ẩn đi
            if (matchesCategory && matchesKeyword) {
                item.style.setProperty("display", "block", "important");
            } else {
                item.style.setProperty("display", "none", "important");
            }
        });
    }

    // 3. ĐỒNG BỘ HAI Ô TÌM KIẾM KHÔNG BỊ LỆCH NHAU
    if (searchDesktop && searchMobile) {
        searchDesktop.addEventListener("input", function () {
            searchMobile.value = searchDesktop.value; // Đồng bộ chữ sang ô Mobile
            filterBooks();
        });

        searchMobile.addEventListener("input", function () {
            searchDesktop.value = searchMobile.value; // Đồng bộ chữ sang ô Desktop
            filterBooks();
        });
    }

    // 4. BỘ LỌC NÚT BẤM NGOÀI TRANG CHỦ
    filterButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            // Thay đổi màu nút đang chọn
            filterButtons.forEach((b) => b.classList.remove("active"));
            this.classList.add("active");

            // Cập nhật thể loại và chạy bộ lọc
            currentCategory = this.getAttribute("data-target");
            
            // Đồng bộ trạng thái active sang cả Menu trái (nếu có trùng danh mục)
            syncMenuLeft(currentCategory);
            
            filterBooks();
        });
    });

    // 5. BỘ LỌC TRONG THANH MENU TRÁI (OFFCANVAS)
    categoryButtons.forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault(); // Ngăn trang web bị cuộn lên đầu khi bấm thẻ <a>

            currentCategory = this.getAttribute("data-filter");

            // Đồng bộ trạng thái active ra nút ngoài trang chủ
            filterButtons.forEach((b) => {
                if (b.getAttribute("data-target") === currentCategory) {
                    b.classList.add("active");
                    b.scrollIntoView({ behavior: "smooth", block: "nearest" }); // Cuộn màn hình nhẹ đến nút đó
                } else {
                    b.classList.remove("active");
                }
            });

            filterBooks();

            // Tự động đóng Menu trái sau khi chọn xong (Tăng trải nghiệm mobile)
            const offcanvasElement = document.getElementById("menuBaGach");
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        });
    });

    // Hàm phụ trợ đồng bộ active từ ngoài vào Menu trái
    function syncMenuLeft(category) {
        categoryButtons.forEach((btn) => {
            if (btn.getAttribute("data-filter") === category) {
                btn.classList.add("text-primary", "fw-bold");
            } else {
                btn.classList.remove("text-primary", "fw-bold");
            }
        });
    }
});