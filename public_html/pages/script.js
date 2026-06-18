/**
 * Logic Hệ thống Tìm kiếm Real-time & Lọc sản phẩm theo Thể loại
 */
document.addEventListener("DOMContentLoaded", function () {
    const searchDesktop = document.getElementById("searchDesktop");
    const searchMobile = document.getElementById("searchMobile");
    const categoryButtons = document.querySelectorAll(".category-btn");
    const bookItems = document.querySelectorAll(".book-item");
    const filterBadge = document.getElementById("filterBadge");
    const noResultAlert = document.getElementById("noResultAlert");
    const offcanvasElement = document.getElementById("menuBaGach");
    
    let currentCategory = "all"; // Trạng thái bộ lọc thể loại mặc định

    // Hàm tổng hợp kiểm tra chéo hai điều kiện lọc
    function filterBooks() {
        // Lấy từ khóa chữ thường từ ô nhập liệu hiện tại
        const keyword = (searchDesktop.value || searchMobile.value).toLowerCase().trim();
        let visibleCount = 0;

        bookItems.forEach(item => {
            const itemCategory = item.getAttribute("data-category");
            const bookName = item.querySelector(".book-name").textContent.toLowerCase();
            const bookAuthor = item.querySelector(".book-author").textContent.toLowerCase();

            // Kiểm tra khớp Thể loại và khớp Từ khóa (Tên sách hoặc Tác giả)
            const matchesCategory = (currentCategory === "all" || itemCategory === currentCategory);
            const matchesKeyword = bookName.includes(keyword) || bookAuthor.includes(keyword);

            if (matchesCategory && matchesKeyword) {
                item.classList.remove("d-none");
                visibleCount++;
            } else {
                item.classList.add("d-none");
            }
        });

        // Hiển thị alert nếu không có sách thỏa mãn điều kiện
        if (visibleCount === 0) {
            noResultAlert.classList.remove("d-none");
        } else {
            noResultAlert.classList.add("d-none");
        }
    }

    // Sự kiện gõ trên thanh tìm kiếm Desktop
    searchDesktop.addEventListener("input", function() {
        searchMobile.value = this.value; // Đồng bộ text sang mobile
        filterBooks();
    });

    // Sự kiện gõ trên thanh tìm kiếm Mobile
    searchMobile.addEventListener("input", function() {
        searchDesktop.value = this.value; // Đồng bộ text sang desktop
        filterBooks();
    });

    // Sự kiện chọn Danh mục Thể loại
    categoryButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            
            currentCategory = this.getAttribute("data-filter");
            const categoryName = this.textContent.trim();

            filterBooks();

            // Cập nhật Badge giao diện thông báo thể loại
            if (currentCategory === "all") {
                filterBadge.classList.add("d-none");
            } else {
                filterBadge.textContent = categoryName;
                filterBadge.classList.remove("d-none");
            }

            // Tự động thu hồi thanh menu Offcanvas sau khi chọn
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (bsOffcanvas) bsOffcanvas.hide();
        });
    });
});