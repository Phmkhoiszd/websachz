-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 06:54 AM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `author`, `price`, `image_path`, `category_id`, `is_best_seller`, `created_at`) VALUES
(1, 'Đắc Nhân Tâm', 'Dale Carnegie', 86000.00, 'images/dacnhantam.jpg', 5, 0, '2026-06-18 15:58:44'),
(2, 'Nhà Giả Kim', 'Paulo Coelho', 79000.00, 'images/nhagiakim.jpg', 2, 0, '2026-06-18 15:58:44'),
(3, 'Tuổi Trẻ Đáng Giá Bao Nhiêu', 'Rosie Nguyễn', 75000.00, 'images/tuoitredanggiabaonhieu.jpg', 5, 0, '2026-06-18 15:58:44'),
(4, 'Hạt Giống Tâm Hồn', 'Many Authors', 50000.00, 'images/hatgiongtamhon.jpg', 5, 0, '2026-06-18 15:58:44'),
(5, 'Nghĩ Giàu Và Làm Giàu', 'Napoleon Hill', 110000.00, 'images/nghigiauvalamgiau.jpg', 3, 0, '2026-06-18 15:58:44'),
(6, 'Cà Phê Cùng Tony', 'Tony Buổi Sáng', 68000.00, 'images/caphecungtony.jpg', 5, 0, '2026-06-18 15:58:44'),
(7, 'Trên Đường Băng', 'Tony Buổi Sáng', 80000.00, 'images/trenduongbang.jpg', 5, 0, '2026-06-18 15:58:44'),
(8, 'Bắt Trẻ Đồng Xanh', 'J. D. Salinger', 65000.00, 'images/battredongxanh.jpg', 2, 0, '2026-06-18 15:58:44'),
(9, 'Lược Sử Thời Gian', 'Stephen Hawking', 125000.00, 'images/luocsuthoigian.jpg', 4, 0, '2026-06-18 15:58:44'),
(10, 'Vũ Trụ Trong Vỏ Hạt Dẻ', 'Stephen Hawking', 140000.00, 'images/vutrutrongvohatde.jpg', 4, 0, '2026-06-18 15:58:44'),
(11, 'Solo Leveling Ragnarok', 'Daul', 95000.00, 'images/sololevelingragnarok.jpg', 6, 0, '2026-06-18 15:58:44'),
(12, 'Một Lít Nước Mắt', 'Kito Aya', 72000.00, 'images/motlitnuocmat.jpg', 2, 0, '2026-06-18 15:58:44');

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `full_name`, `created_at`) VALUES
(1, 'kkkkk', 'khoip8500@gmail.com', '$2y$10$2xuhz4AYMFwUykm17keGgO4w3jUHMBIJSnOTq7F9c4UZZp7K68WkG', 'kkkkk', '2026-06-18 16:32:08');

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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
