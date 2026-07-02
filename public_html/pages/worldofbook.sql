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

create table `books` (
  `book_id` int(11) not null,
  `book_name` varchar(255) not null,
  `author` varchar(150) not null,
  `price` decimal(10,2) not null,
  `image_path` varchar(255) not null,
  `category_id` int(11) default null,
  `is_best_seller` tinyint(1) default 0,
  `created_at` timestamp not null default current_timestamp()
) engine=innodb default charset=utf8mb4 collate=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

insert into `books` (`book_id`, `book_name`, `author`, `price`, `image_path`, `category_id`, `is_best_seller`, `created_at`) values
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

create table `carts` (
  `cart_id` int(11) not null,
  `user_id` int(11) not null,
  `book_id` int(11) not null,
  `quantity` int(11) not null default 1,
  `added_at` timestamp not null default current_timestamp()
) engine=innodb default charset=utf8mb4 collate=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

create table `categories` (
  `category_id` int(11) not null,
  `category_slug` varchar(100) not null unique,
  `category_name` varchar(150) not null
) engine=innodb default charset=utf8mb4 collate=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

insert into `categories` (`category_id`, `category_slug`, `category_name`) values
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

create table `users` (
  `user_id` int(11) not null,
  `username` varchar(100) not null unique,
  `email` varchar(150) not null unique,
  `password_hash` varchar(255) not null,
  `full_name` varchar(255) default null,
  `created_at` timestamp not null default current_timestamp()
) engine=innodb default charset=utf8mb4 collate=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

insert into `users` (`user_id`, `username`, `email`, `password_hash`, `full_name`, `created_at`) values
(1, 'kkkkk', 'khoip8500@gmail.com', '$2y$10$2xuhz4AYMFwUykm17keGgO4w3jUHMBIJSnOTq7F9c4UZZp7K68WkG', 'kkkkk', '2026-06-18 16:32:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
alter table `books`
  add primary key (`book_id`),
  add key `category_id` (`category_id`);

--
-- Indexes for table `carts`
--
alter table `carts`
  add primary key (`cart_id`),
  add key `user_id` (`user_id`),
  add key `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
alter table `categories`
  add primary key (`category_id`),
  add unique key `category_slug` (`category_slug`);

--
-- Indexes for table `users`
--
alter table `users`
  add primary key (`user_id`),
  add unique key `username` (`username`),
  add unique key `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
alter table `books`
  modify `book_id` int(11) not null auto_increment, auto_increment=13;

--
-- AUTO_INCREMENT for table `carts`
--
alter table `carts`
  modify `cart_id` int(11) not null auto_increment;

--
-- AUTO_INCREMENT for table `categories`
--
alter table `categories`
  modify `category_id` int(11) not null auto_increment, auto_increment=7;

--
-- AUTO_INCREMENT for table `users`
--
alter table `users`
  modify `user_id` int(11) not null auto_increment, auto_increment=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
alter table `books`
  add constraint `books_ibfk_1` foreign key (`category_id`) references `categories` (`category_id`) on delete set null;

--
-- Constraints for table `carts`
--
alter table `carts`
  add constraint `carts_ibfk_1` foreign key (`user_id`) references `users` (`user_id`) on delete cascade,
  add constraint `carts_ibfk_2` foreign key (`book_id`) references `books` (`book_id`) on delete cascade;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
