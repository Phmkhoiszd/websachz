-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 07:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `worldofbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `author` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sales_count` int(11) DEFAULT 0,
  `discount_percent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `author`, `price`, `image_path`, `category_id`, `is_best_seller`, `created_at`, `sales_count`, `discount_percent`) VALUES
(1, 'Đắc Nhân Tâm', 'Dale Carnegie', 86000.00, 'images/dacnhantam.jpg', 5, 0, '2026-06-18 15:58:44', 0, 20),
(2, 'Nhà Giả Kim', 'Paulo Coelho', 79000.00, 'images/nhagiakim.jpg', 2, 0, '2026-06-18 15:58:44', 0, 15),
(3, 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'Rosie Nguyễn', 75000.00, 'images/tuoitredanggiabaonhieu.jpg', 5, 0, '2026-06-18 15:58:44', 150, 0),
(4, 'Hạt Giống Tâm Hồn', 'Many Authors', 50000.00, 'images/hatgiongtamhon.jpg', 5, 0, '2026-06-18 15:58:44', 95, 0),
(5, 'Nghĩ Giàu Và Làm Giàu', 'Napoleon Hill', 110000.00, 'images/nghigiauvalamgiau.jpg', 3, 0, '2026-06-18 15:58:44', 0, 0),
(6, 'Cà Phê Cùng Tony', 'Tony Buổi Sáng', 68000.00, 'images/caphecungtony.jpg', 5, 0, '2026-06-18 15:58:44', 0, 0),
(7, 'Trên Đường Băng', 'Tony Buổi Sáng', 80000.00, 'images/trenduongbang.jpg', 5, 0, '2026-06-18 15:58:44', 0, 0),
(8, 'Bắt Trẻ Đồng Xanh', 'J. D. Salinger', 65000.00, 'images/battredongxanh.jpg', 2, 0, '2026-06-18 15:58:44', 0, 0),
(9, 'Lược Sử Thời Gian', 'Stephen Hawking', 125000.00, 'images/luocsuthoigian.jpg', 4, 0, '2026-06-18 15:58:44', 0, 0),
(10, 'Vũ Trụ Trong Vỏ Hạt Dẻ', 'Stephen Hawking', 140000.00, 'images/vutrutrongvohatde.jpg', 4, 0, '2026-06-18 15:58:44', 0, 0),
(11, 'Solo Leveling Ragnarok', 'Daul', 95000.00, 'images/sololevelingragnarok.jpg', 6, 0, '2026-06-18 15:58:44', 0, 0),
(12, 'Một Lít Nước Mắt', 'Kito Aya', 72000.00, 'images/motlitnuocmat.jpg', 2, 0, '2026-06-18 15:58:44', 0, 0),
(13, 'Astro Boy (Tetsuwan Atom)', 'Osamu Tezuka', 45000.00, 'images/astroboy.jpg', 6, 0, '2026-06-22 17:17:44', 0, 0),
(14, 'Blue Lock', 'Muneyuki Kaneshiro (Yusuke Nomura minh họa)', 50000.00, 'images/bluelock.jpg', 6, 0, '2026-06-22 17:17:44', 0, 0),
(15, 'Thám Tử Lừng Danh Conan', 'Gosho Aoyama', 30000.00, 'images/conan.jpg', 6, 1, '2026-06-22 17:17:44', 0, 0),
(16, 'Bảy Viên Ngọc Rồng (Dragon Ball)', 'Akira Toriyama', 40000.00, 'images/dragonball.jpg', 6, 1, '2026-06-22 17:17:44', 0, 0),
(17, 'Haikyuu!! (Chàng Trai Bóng Chuyền)', 'Haruichi Furudate', 45000.00, 'images/haikyuu.jpg', 6, 0, '2026-06-22 17:17:44', 0, 0),
(18, 'Vương Giả Thiên Hạ (Kingdom)', 'Yasuhisa Hara', 55000.00, 'images/kingdom.jpg', 6, 0, '2026-06-22 17:17:44', 0, 0),
(19, 'Đảo Hải Tặc (One Piece)', 'Eiichiro Oda', 40000.00, 'images/onepiece.jpg', 6, 1, '2026-06-22 17:17:44', 0, 0),
(20, 'One-Punch Man', 'ONE (Yusuke Murata minh họa)', 45000.00, 'images/onepunchman.jpg', 6, 0, '2026-06-22 17:17:44', 0, 0),
(21, 'Hai Vạn Dặm Dưới Đáy Biển', 'Jules Verne', 95000.00, 'images/haivandamduoidaybien.jpg', 2, 0, '2026-06-22 17:17:44', 0, 0),
(22, 'Đất Rừng Phương Nam', 'Đoàn Giỏi', 85000.00, 'images/datrungphuongnam.jpg', 2, 0, '2026-06-22 17:17:44', 0, 0),
(23, 'Hoàng Tử Bé', 'Antoine de Saint-Exupéry (Nguyễn Thành Long dịch)', 60000.00, 'images/hoangtube.jpg', 2, 0, '2026-06-22 17:17:44', 0, 10),
(24, 'Mưa Đỏ', 'Chu Lai', 120000.00, 'images/muado.jpg', 2, 0, '2026-06-22 17:17:44', 0, 0),
(25, 'Ông Già Và Biển Cả', 'Ernest Hemingway (Lê Huy Bắc - Hoàng Hữu Phê dịch)', 75000.00, 'images/onggiavabienca.jpg', 2, 0, '2026-06-22 17:17:44', 0, 0),
(26, 'Sống Mòn', 'Nam Cao', 70000.00, 'images/songmon.jpg', 2, 0, '2026-06-22 17:17:44', 0, 0),
(27, 'AI Tạo Sinh (AI and Innovation)', 'Michael Lewrick & Omar Hatamleh (Tùng Bách Mộc dịch)', 180000.00, 'images/aitaosinh.jpg', 3, 0, '2026-06-22 17:17:44', 0, 15),
(28, 'Bán Hàng Cho Những Gã Khổng Lồ', 'Jill Konrath (Thảo Nguyên dịch)', 135000.00, 'images/banhangchonhunggakhonglo.jpg', 3, 0, '2026-06-22 17:17:44', 0, 0),
(29, 'Bước Chuyển Bản Thể (A Shift in Being)', 'Leon VanderPol (Thục Linh dịch)', 150000.00, 'images/buocchuyenbanthe.jpg', 5, 0, '2026-06-22 17:17:44', 0, 0),
(30, 'Cẩm Nang Sự Nghiệp Hậu Tốt Nghiệp', 'Steve Rook (Mia dịch)', 98000.00, 'images/camnangsunghiephautotnghiep.jpg', 5, 0, '2026-06-22 17:17:44', 0, 0),
(31, 'Khi Loài Cá Biến Mất', 'Mark Kurlansky (Lê Nhật Thắng dịch)', 115000.00, 'images/khiloaicabienmat.jpg', 4, 0, '2026-06-22 17:17:44', 0, 0),
(32, 'The Bunny and Turtle', 'Muneeara Rafique', 65000.00, 'images/bunnyandturtle.jpg', 1, 0, '2026-06-22 17:17:44', 0, 0),
(33, 'Growing Towhehere', 'Master Hoo', 65000.00, 'images/growingtowhehere.jpg', 1, 0, '2026-06-22 17:17:44', 0, 0),
(34, 'Gia Đình Nhỏ Hạnh Phúc To (Phần 2)', 'Nguyễn Văn Chung', 110000.00, 'images/giadinhnhohanhphucto.jpg', 1, 0, '2026-06-22 17:17:44', 0, 0),
(35, 'Family Guy', 'Seth MacFarlane', 0.00, 'images/familyguy.jpg', NULL, 0, '2026-06-22 17:17:44', 0, 0),
(36, 'Tom and Jerry (1960s)', 'William Hanna & Joseph Barbera', 0.00, 'images/tomandjerry.jpg', NULL, 0, '2026-06-22 17:17:44', 0, 0),
(37, 'Trí Tuệ Nhân Tạo: Thách Thức Đối Với Nhân Sự IT', 'xper', 0.00, 'images/trituenhantao.jpg', NULL, 0, '2026-06-22 17:17:44', 0, 0),
(38, 'Poster Động Lực: \"Đích đến sẽ không thay đổi...\"', 'Khuyết Danh', 0.00, 'images/dichdensekhongthaydoineubankhongthaydoiconduong.jpg', NULL, 0, '2026-06-22 17:17:44', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `book_id`, `quantity`, `added_at`) VALUES
(10, 1, 2, 2, '2026-06-21 15:45:59'),
(11, 1, 3, 1, '2026-06-22 16:09:51'),
(12, 1, 10, 1, '2026-06-22 16:24:00'),
(13, 1, 1, 1, '2026-06-22 16:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_slug` varchar(50) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_slug`, `category_name`) VALUES
(1, 'tre-em', 'Trẻ em'),
(2, 'van-hoc', 'Văn học & Tiểu thuyết'),
(3, 'kinh-te', 'Kinh tế - Kinh doanh'),
(4, 'khoa-hoc', 'Khoa học công nghệ'),
(5, 'ky-nang', 'Sách kỹ năng sống'),
(6, 'manga', 'Truyện tranh - Manga');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `full_name`, `email`, `phone`, `address`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 'kkkkk', 'khppp@gmail.com', '342345233', '23422', 310000.00, 'Pending', '2026-06-21 04:37:45'),
(2, 1, 'kkkkk', 'kkkkk2@Q', '2131', '23435fgre', 703000.00, 'Pending', '2026-06-21 15:36:12');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 79000.00),
(2, 1, 4, 1, 50000.00),
(3, 1, 1, 1, 86000.00),
(4, 1, 11, 1, 95000.00),
(5, 2, 7, 5, 80000.00),
(6, 2, 3, 1, 75000.00),
(7, 2, 4, 1, 50000.00),
(8, 2, 5, 1, 110000.00),
(9, 2, 6, 1, 68000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `full_name`, `created_at`) VALUES
(1, 'kkkkk', 'khoip8500@gmail.com', '$2y$10$2xuhz4AYMFwUykm17keGgO4w3jUHMBIJSnOTq7F9c4UZZp7K68WkG', 'user', 'kkkkk', '2026-06-18 16:32:08'),
(2, 'dinh1111', 'dinh1976@gmail.com', '$2y$10$S9G/N0U7rA.W76L.bH8eI.mBw688/A6zH6EBlwUvVdO2eN7hREdO.', 'admin', NULL, '2026-06-21 04:24:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_slug` (`category_slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
