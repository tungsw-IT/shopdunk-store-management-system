-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2026 at 12:49 PM
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
-- Database: `db_shop_clean`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `accounts_name` varchar(100) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `accounts_name`, `email`, `phone`, `password`, `reset_code`, `created_at`) VALUES
(1, NULL, 'hi@gmail.com', '0978255997', '$2y$10$.3ruWBr9TW8GZ9/GXGka1u22UhQhfMqniEP40oHfQSNdJKsJ1ckHu', NULL, '2026-04-09 07:22:27'),
(2, NULL, 'truong@gmail.com', '0989898989', '$2y$10$XNvcpJYfkBCI20U1PY/wa.cX1jTq2L2Fa9wpCr49UHt9Yu3GqSVIe', NULL, '2026-04-09 20:51:02'),
(3, NULL, 'tung@gmail.com', '0342657822', '$2y$10$Jl4dBXWg24FvCjAIA2sqAepygybcuWpgo7PMPk6oOeUC.ADH6v8fu', NULL, '2026-04-22 17:25:22'),
(4, NULL, 'xuan@gmail.com', '0978255998', '$2y$10$Xl05o8.kFBwC.wkM9jMtiO7piKT/66Im9RIx.JfN7du4MTERq7LsC', NULL, '2026-04-22 18:36:49'),
(5, NULL, 'tu@gmail.com', '0978255999', '$2y$10$A7c.lNnB/K7.wRp47uzNbOOqFZKaHnb0begIluYQkcnr1vVOyx9KG', NULL, '2026-04-22 18:38:41'),
(6, NULL, 'auto1776949396.436072@gmail.com', '0123456789', '$2y$10$1T3BE5XoAnQa7JRkcURRHuts7Jts2FzE5lL9SBVtjOu2CzJS4c8Ju', NULL, '2026-04-23 13:03:19'),
(7, NULL, 'demo5@gmail.com', '0123456785', '$2y$10$LWsXSPe1m5VTLjZHktCsNO.qfhJrwccFwjCWo/z43iv86jHhu6SXG', NULL, '2026-07-09 05:51:32'),
(8, NULL, 'demo2@gmail.com', '0987654321', '$2y$10$pWYLIDm91GHH/1xZhicrneIN0XidIVAHwv12UpSSwYYS/zf7BW7B6', NULL, '2026-07-09 06:09:09'),
(9, NULL, 'demo5@example.com', '0987654324', '$2y$10$7BW94oDRa4JYvSCFnpPlXuvNzYySuj1ryN4d1R5Fdg6vt97yrJT9i', NULL, '2026-07-09 06:11:41'),
(11, 'Login Test', 'login_test_local@example.com', '0999999999', '$2y$10$OA73fxdLY1PkVPIRV7NcYu2mizCeUR2q7JWt9mzmQx47Znv3h4j5W', NULL, '2026-07-09 09:39:27'),
(12, NULL, 'my@gmail.com', '0989989989', '$2y$12$4BflbcFwA/ZQ9P4CrphS8OBJH8do7.UDVYSpG0n92zIB10/yRYJqi', NULL, '2026-09-14 07:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_username` varchar(100) NOT NULL,
  `admin_full_name` varchar(100) DEFAULT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_role` varchar(50) NOT NULL DEFAULT 'quan_ly',
  `admin_status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_username`, `admin_full_name`, `admin_password`, `admin_role`, `admin_status`, `created_at`, `updated_at`) VALUES
('admin@gmail.com', NULL, '$2y$12$uB5Txcvkl2kfXBXNjAlryOsqQxY6xANGFU5jsRsrrTtIM89rJLW.2', 'quan_ly_tong', 'active', '2026-04-22 18:50:29', '2026-09-14 07:50:22'),
('hue123', 'Huệ', '$2y$10$mYSNZw0ivGzoOyBDeJOxg.oiL22ax8WkHojJGQWiJ8H3DYq6dxr1i', 'nhan_vien_ban_hang', 'active', '2026-04-09 20:03:30', '2026-04-09 20:03:30'),
('mai123', 'mai', '$2y$10$bpM9CsyHnkR53oo/Ux0fwOOtIjKuT8pg/GDfEAC6o/gX2qfiVpzqC', 'nhan_vien_ban_hang', 'active', '2026-04-09 20:02:58', '2026-04-09 20:02:58'),
('nga123', 'nga', '$2y$10$R6XoHdEYoH2Nao7XZWqfj.qYGI8RpSHw5k6YaGVJi.pyZ8rEEvf9G', 'to_truong_ban_hang', 'active', '2026-04-09 20:03:13', '2026-04-09 20:03:13'),
('thuy123', 'thúy', '$2y$10$Cq8UBOyXBRoMqPq1prvtLObToIljnBKFHGk7QUJm6K04I4SrKU1ua', 'thu_ngan_cskh', 'active', '2026-04-10 07:04:28', '2026-04-10 07:04:28'),
('truong123', 'Truong', '$2y$10$hdYnGl7JdSTL6yYUhuJCB.IOv8Pv6jERKqXLiMJSKq/BEIPJYA8vK', 'ky_thuat_vien', 'active', '2026-04-09 20:04:16', '2026-04-09 20:04:16'),
('tung123', NULL, '$2y$10$iSZx/Bg8RkynYRPFPngsMOXwBgwp8pocrw/ki1qmQ.yKi.k2agalW', 'quan_ly_tong', 'active', '2026-04-09 07:27:52', '2026-04-09 07:27:52'),
('Xuan123', 'Xuan', '$2y$10$lKs5DjuLE5/Ne0NLVzfeiu5.M5UVF1uDN8imqzgEIboD7Kp0U3uSO', 'quan_ly', 'active', '2026-04-09 20:03:53', '2026-04-09 20:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(120) NOT NULL,
  `brand_code` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `brand_code`, `note`, `created_at`) VALUES
(1, 'Apple', 'APPLE', 'Thuong hieu mac dinh', '2026-04-09 08:14:10');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `account_id` int(11) NOT NULL,
  `product_imei` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`account_id`, `product_imei`, `quantity`, `created_at`) VALUES
(1, 'UsA', 1, '2026-04-09 18:11:07'),
(2, 'apwvn', 2, '2026-04-09 22:52:38'),
(2, 'ip15vn', 3, '2026-04-09 22:38:30'),
(2, 'IPMN7VN', 3, '2026-04-09 22:38:44'),
(2, 'Vn', 1, '2026-04-10 05:00:27'),
(5, 'UsA', 3, '2026-07-08 18:51:36');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(120) NOT NULL,
  `customer_gender` varchar(20) DEFAULT NULL,
  `customer_email` varchar(191) DEFAULT NULL,
  `customer_contact_no` varchar(30) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_gender`, `customer_email`, `customer_contact_no`, `customer_address`, `created_at`) VALUES
(1, 'Đoàn Quang Trường', 'male', 'truong@gmail.com', '0998989989', '54 Triều Khúc,Hà Nội, Hà Nội, Viet Nam', '2026-04-09 08:26:21'),
(2, 'Hà Thanh Tùng', 'Nam', 'tungha16012005@gmail.com', '0978255997', 'Thái Bình,Việt Nam\r\nHà Nội,Việt Nam', '2026-04-22 18:52:50'),
(4, 'tu', 'Nam', 'tu@gmail.com', '0988777999', 'Thái Bình,Việt Nam\r\nHà Nội,Việt Nam', '2026-04-22 19:23:31'),
(5, 'Nguyen Van A', 'Nam', 'tc01@gmail.com', '0123456789', 'Hanoi', '2026-04-23 13:01:49'),
(6, 'Đào Thị Ngân', 'female', 'ngandao@gmail.com', '0983810261', '54 Triều Khúc, Hà Nội, Viet Nam', '2026-04-26 15:25:59'),
(7, 'Demo User', '', 'demo5@example.com', '0987654324', '123 ABC', '2026-07-09 06:12:39'),
(8, 'Hà Thanh Tùng', 'male', 'tunggc.161@gmail.com', '978255997', 'Lê Lơik, Tiên Quang, Viet Nam', '2026-09-14 10:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `mobile`
--

CREATE TABLE `mobile` (
  `company_name` varchar(100) NOT NULL,
  `company_series` varchar(100) NOT NULL,
  `model_no` varchar(100) NOT NULL,
  `imei_number` varchar(50) NOT NULL,
  `ram(GB)` int(11) NOT NULL DEFAULT 0,
  `rom(GB)` int(11) NOT NULL DEFAULT 0,
  `display_size(inchi)` decimal(4,2) NOT NULL DEFAULT 0.00,
  `display_quality` varchar(100) DEFAULT NULL,
  `processor` varchar(150) DEFAULT NULL,
  `battery_capacity(mah)` int(11) NOT NULL DEFAULT 0,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `old_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobile`
--

INSERT INTO `mobile` (`company_name`, `company_series`, `model_no`, `imei_number`, `ram(GB)`, `rom(GB)`, `display_size(inchi)`, `display_quality`, `processor`, `battery_capacity(mah)`, `color`, `price`, `old_price`, `stock_quantity`) VALUES
('Apple', 'iPhone', 'Iphone17 512G', '167wskds', 8, 2048, 6.30, 'Fukl Hh', 'Chipo A19', 5000, 'tÍM', 54000000.00, 60000000.00, 100),
('Apple', 'iPhone', 'iPhone 17 Pro Max 256GB', '9284928493248', 8, 1024, 6.90, '2868 x 1320 pixels', 'Chip A19 Pro', 6700, 'Cam', 45000000.00, 57000000.00, 100),
('Apple', 'iPhone', 'iPhone 17 Pro Max 256GB', '92849284939', 8, 256, 6.90, '2868 x 1320 pixels', 'Chip A19 Pro', 6700, 'Cam', 34500000.00, 37990000.00, 100),
('Apple', 'MacBook', 'MacBook Air 15 M5 2026 10CPU/10GPU/16GB/1TB/35W', 'A15vnn', 8, 1024, 15.30, 'Liquid Retina', 'M5', 35000, 'Xanh catana', 40190000.00, 40490000.00, 100),
('Apple', 'Watch', 'Apple Watch Ultra 3 GPS + Cellular 49mm | Alpine Loop (2025)', 'apwvn', 4, 128, 49.00, 'OLED', 'Apple S10', 2000, 'Xanh green', 22990000.00, 23900000.00, 100),
('Apple', 'iPad', 'iPad Pro 13 M5 2025 WiFi', 'IP13vN', 8, 2048, 13.00, 'Ultra Liquid Retina XDR', 'Apple M5', 6000, 'Bạc', 60990000.00, 65000000.00, 100),
('Apple', 'iPhone', 'iPhone 15 Pro Max 256GB', 'ip15vn', 8, 256, 6.70, 'Màn hình Super Retina XDR', 'iOS 17', 7000, 'Titan trắng', 26990000.00, 37990000.00, 98),
('Apple', 'iPad', 'iPad A16 5G 128GB', 'IPA16VN', 8, 128, 11.00, 'Liquid Retina HD', 'Apple A16', 4000, 'Bạc', 13790000.00, 15000000.00, 100),
('Apple', 'iPad', 'iPad Mini 7 2024 WiFi 128GB', 'IPMN7VN', 8, 128, 8.30, 'Liquid Retina', 'Apple A17 Pro', 4600, 'Xám', 13490000.00, 15340000.00, 100),
('Apple', 'iPad', 'Ipad Pro M5 2025 WiFi 12GB 256GB', 'IPVN', 6, 1024, 11.00, 'Ultra Liquid Retina XDR', 'Apple GPU 10 nhân', 6000, 'Đen huyền bí', 28990000.00, 30000000.00, 99),
('Apple', 'MacBook', 'MacBook Neo 13 8GB/256GB/20W', 'MBN13VN', 8, 256, 13.00, 'Liquid Retina', 'Kiểu ổ cứng SSD', 30790, 'Xanh lagle', 16490000.00, 18000000.00, 100),
('Apple', 'MacBook', 'MacBook Pro 14 M5 2025 10CPU/10GPU/16GB/1TB', 'MBPR', 8, 1024, 14.20, 'Liquid Retina XDR', 'M5', 72400, 'Đen', 44490000.00, 46890000.00, 100),
('Apple', 'MacBook', 'MacBook Pro 16 M5 Pro 2026 18CPU/20GPU/24GB/1TB/140W', 'P15VN', 8, 1024, 16.20, 'Liquid Retina', 'M5', 140000, 'Bạc', 71990000.00, 73990000.00, 100),
('Apple', 'Watch', 'Apple Watch Series 11 Titan (GPS + Cellular) 46mm Milanese Loop size S/M', 's11vn', 4, 128, 46.00, 'Oled', 'Apple S10', 2000, 'Xám', 22990000.00, 23000000.00, 100),
('Apple', 'Watch', 'Apple Watch Series 10 Nhôm (GPS) 46mm | Sport Loop', 'SAPW10', 4, 128, 46.00, 'oLED', 'Apple5', 2000, 'Đen', 11290000.00, 11990000.00, 100),
('Apple', 'Watch', 'Apple Watch SE 3 Nhôm (GPS) 40mm Sport Band size S/M', 'sevn', 4, 128, 40.00, 'OLED', 'Apple S10', 1500, 'Đen', 6490000.00, 7000000.00, 100),
('Apple', 'iPhone', '17e', 'UsA', 4, 128, 6.30, 'Super Retina XDR độ sáng 1200 nits', 'A19', 3500, 'Pink', 17590000.00, 18000000.00, 99),
('Apple', 'iPhone', 'Iphone 17 Promax Bạc', 'Vn', 8, 2048, 6.90, 'Super Retina XDR OLED', 'A19 Pro', 6700, 'Cam', 55000000.00, 60000000.00, 100),
('Apple', 'iPhone', 'Iphone 17', 'Vn234', 8, 1024, 6.30, 'Màn hình Super Retina XDR◊Tham khảo tuyên bố từ chối trách nhiệm pháp lý. Công nghệ ProMotion', 'A19', 2800, 'Xanh blue', 24190000.00, 24500000.00, 99);

-- --------------------------------------------------------

--
-- Table structure for table `operations_tasks`
--

CREATE TABLE `operations_tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `assigned_role` varchar(50) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operations_tasks`
--

INSERT INTO `operations_tasks` (`id`, `title`, `description`, `status`, `assigned_role`, `due_date`, `updated_at`) VALUES
(1, 'Xếp hàng', 'Trao đổi thông tin', 'pending', 'quan_ly', '2026-04-22', '2026-04-22 01:38:28'),
(2, 'Xếp hàng', 'trao đổi', 'pending', 'quan_ly', '2026-04-22', '2026-04-22 01:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `paymentbill`
--

CREATE TABLE `paymentbill` (
  `pb_id` int(11) NOT NULL,
  `pb_date` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(120) NOT NULL,
  `customer_contact_no` varchar(30) DEFAULT NULL,
  `purchase_mobile_imei_no` varchar(50) NOT NULL,
  `purchase_mobile_company` varchar(100) NOT NULL,
  `purchase_mobile_series` varchar(100) NOT NULL,
  `purchase_mobile_model` varchar(100) NOT NULL,
  `purchase_mobile_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paymentbill`
--

INSERT INTO `paymentbill` (`pb_id`, `pb_date`, `customer_id`, `customer_name`, `customer_contact_no`, `purchase_mobile_imei_no`, `purchase_mobile_company`, `purchase_mobile_series`, `purchase_mobile_model`, `purchase_mobile_price`, `total_paid_amount`, `created_at`, `status`) VALUES
(1, '09-04-2026', 1, 'Đoàn Quang Trường', '0998989989', 'Vn', 'Apple', 'iPhone', 'Iphone 17 Promax', 55000000.00, 55000000.00, '2026-04-09 08:26:21', 'pending'),
(2, '09-04-2026', 1, 'Đoàn Quang Trường', '0998989989', 'A15vnn', 'Apple', 'MacBook', 'MacBook Air 15 M5 2026 10CPU/10GPU/16GB/1TB/35W', 40190000.00, 40190000.00, '2026-04-09 21:09:36', 'pending'),
(3, '09-04-2026', 1, 'Đoàn Quang Trường', '0998989989', 'ip15vn', 'Apple', 'iPhone', 'iPhone 15 Pro Max 256GB', 26990000.00, 26990000.00, '2026-04-09 21:25:41', 'pending'),
(4, '09-04-2026', 1, 'Đoàn Quang Trường', '0998989989', 'UsA', 'Apple', 'iPhone', '17e', 17590000.00, 17590000.00, '2026-04-09 21:25:41', 'pending'),
(5, '26-04-2026', 6, 'Đào Thị Ngân', '0983810261', 'Vn234', 'Apple', 'iPhone', 'Iphone 17', 24190000.00, 24190000.00, '2026-04-26 15:25:59', 'pending'),
(6, '09-07-2026', 7, 'Demo User', '0987654324', 'sevn', 'Apple', 'Watch', 'Apple Watch SE 3 Nhôm (GPS) 40mm Sport Band size S/M', 6490000.00, 6490000.00, '2026-07-09 06:12:39', 'pending'),
(7, '2026-09-14', 12, 'Hà Thanh Tùng', '0978255997', '9284928493248', 'Apple', 'iPhone', 'iPhone 17 Pro Max 256GB', 45000000.00, 45000000.00, '2026-09-14 08:36:27', 'pending'),
(8, '2026-09-14', 12, 'Hà Thanh Tùng', '0978255997', 'IP13vN', 'Apple', 'iPad', 'iPad Pro 13 M5 2025 WiFi', 60990000.00, 60990000.00, '2026-09-14 08:36:27', 'pending'),
(9, '14-09-2026', 8, 'Hà Thanh Tùng', '978255997', 'ip15vn', 'Apple', 'iPhone', 'iPhone 15 Pro Max 256GB', 26990000.00, 26990000.00, '2026-09-14 10:16:59', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `imei_number` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `imei_number`, `image_path`, `sort_order`, `created_at`) VALUES
(1, 'UsA', 'images/product_gallery/UsA_20260409173816_0.jpeg', 1, '2026-04-09 15:38:16'),
(2, 'UsA', 'images/product_gallery/UsA_20260409193318_0.jpeg', 1, '2026-04-09 17:33:18'),
(3, 'UsA', 'images/product_gallery/UsA_20260409193328_0.jpeg', 1, '2026-04-09 17:33:28'),
(6, 'IPVN', 'images/product_gallery/IPVN_20260409211643_0.webp', 1, '2026-04-09 19:16:43'),
(7, 'IPVN', 'images/product_gallery/IPVN_20260409211745_0.webp', 1, '2026-04-09 19:17:45'),
(8, 'IPVN', 'images/product_gallery/IPVN_20260409211754_0.webp', 1, '2026-04-09 19:17:54'),
(13, 'IPA16VN', 'images/product_gallery/IPA16VN_20260409212529_0.webp', 1, '2026-04-09 19:25:29'),
(14, 'IPA16VN', 'images/product_gallery/IPA16VN_20260409212547_0.webp', 1, '2026-04-09 19:25:47'),
(15, 'IPA16VN', 'images/product_gallery/IPA16VN_20260409212558_0.webp', 1, '2026-04-09 19:25:58'),
(16, 'IPMN7VN', 'images/product_gallery/IPMN7VN_20260409212807_0.webp', 1, '2026-04-09 19:28:07'),
(17, 'IPMN7VN', 'images/product_gallery/IPMN7VN_20260409212827_0.webp', 1, '2026-04-09 19:28:27'),
(18, 'IPMN7VN', 'images/product_gallery/IPMN7VN_20260409212834_0.webp', 1, '2026-04-09 19:28:34'),
(19, 'MBPR', 'images/product_gallery/MBPR_20260409213147_0.webp', 1, '2026-04-09 19:31:47'),
(20, 'MBPR', 'images/product_gallery/MBPR_20260409213219_0.webp', 1, '2026-04-09 19:32:19'),
(21, 'MBPR', 'images/product_gallery/MBPR_20260409213230_0.webp', 1, '2026-04-09 19:32:30'),
(22, 'MBN13VN', 'images/product_gallery/MBN13VN_20260409213602_0.webp', 1, '2026-04-09 19:36:02'),
(23, 'MBN13VN', 'images/product_gallery/MBN13VN_20260409213614_0.webp', 1, '2026-04-09 19:36:14'),
(24, 'MBN13VN', 'images/product_gallery/MBN13VN_20260409213624_0.webp', 1, '2026-04-09 19:36:24'),
(25, 'P15VN', 'images/product_gallery/P15VN_20260409213900_0.webp', 1, '2026-04-09 19:39:00'),
(26, 'P15VN', 'images/product_gallery/P15VN_20260409213908_0.webp', 1, '2026-04-09 19:39:08'),
(27, 'P15VN', 'images/product_gallery/P15VN_20260409213918_0.webp', 1, '2026-04-09 19:39:18'),
(28, 'A15vnn', 'images/product_gallery/A15vnn_20260409214211_0.webp', 1, '2026-04-09 19:42:11'),
(29, 'A15vnn', 'images/product_gallery/A15vnn_20260409214222_0.webp', 1, '2026-04-09 19:42:22'),
(30, 'A15vnn', 'images/product_gallery/A15vnn_20260409214231_0.webp', 1, '2026-04-09 19:42:31'),
(31, 'apwvn', 'images/product_gallery/apwvn_20260409214824_0.jpeg', 1, '2026-04-09 19:48:24'),
(32, 'apwvn', 'images/product_gallery/apwvn_20260409214834_0.jpeg', 1, '2026-04-09 19:48:34'),
(33, 'apwvn', 'images/product_gallery/apwvn_20260409214843_0.jpeg', 1, '2026-04-09 19:48:43'),
(34, 'SAPW10', 'images/product_gallery/SAPW10_20260409215118_0.jpeg', 1, '2026-04-09 19:51:18'),
(35, 'SAPW10', 'images/product_gallery/SAPW10_20260409215224_0.jpeg', 1, '2026-04-09 19:52:24'),
(36, 'SAPW10', 'images/product_gallery/SAPW10_20260409215233_0.jpeg', 1, '2026-04-09 19:52:33'),
(37, 's11vn', 'images/product_gallery/s11vn_20260409215518_0.jpeg', 1, '2026-04-09 19:55:18'),
(38, 's11vn', 'images/product_gallery/s11vn_20260409215534_0.jpeg', 1, '2026-04-09 19:55:34'),
(39, 's11vn', 'images/product_gallery/s11vn_20260409215544_0.jpeg', 1, '2026-04-09 19:55:44'),
(40, 'sevn', 'images/product_gallery/sevn_20260409215745_0.jpeg', 1, '2026-04-09 19:57:45'),
(41, 'sevn', 'images/product_gallery/sevn_20260409215756_0.jpeg', 1, '2026-04-09 19:57:56'),
(42, 'sevn', 'images/product_gallery/sevn_20260409215806_0.jpeg', 1, '2026-04-09 19:58:06'),
(43, 'ip15vn', 'images/product_gallery/ip15vn_20260409220056_0.png', 1, '2026-04-09 20:00:56'),
(44, 'ip15vn', 'images/product_gallery/ip15vn_20260409220108_0.png', 1, '2026-04-09 20:01:08'),
(45, 'ip15vn', 'images/product_gallery/ip15vn_20260409220108_1.png', 2, '2026-04-09 20:01:08'),
(47, '9284928493248', 'images/product_gallery/9284928493248_20260914075534_0.webp', 1, '2026-09-14 07:55:34'),
(48, '92849284939', 'images/product_gallery/92849284939_20260914075629_0.webp', 1, '2026-09-14 07:56:29'),
(49, '167wskds', 'images/product_gallery/167wskds_20260914080125_0.webp', 1, '2026-09-14 08:01:25'),
(50, '167wskds', 'images/product_gallery/167wskds_20260914080232_0.webp', 1, '2026-09-14 08:02:32'),
(51, 'Vn234', 'images/product_gallery/Vn234_20260914080328_0.webp', 1, '2026-09-14 08:03:28'),
(52, 'Vn', 'images/product_gallery/Vn_20260914080553_0.webp', 1, '2026-09-14 08:05:53'),
(53, 'IP13vN', 'images/product_gallery/IP13vN_20260914081322_0.webp', 1, '2026-09-14 08:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promo_code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` varchar(20) NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair_requests`
--

CREATE TABLE `repair_requests` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(120) NOT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `device_model` varchar(120) NOT NULL,
  `issue_description` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `technician_username` varchar(100) DEFAULT NULL,
  `request_date` date NOT NULL,
  `completed_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_requests`
--

INSERT INTO `repair_requests` (`id`, `customer_name`, `contact_phone`, `device_model`, `issue_description`, `status`, `technician_username`, `request_date`, `completed_date`, `updated_at`) VALUES
(1, 'Đoàn Quang Trường', '0989898989', 'Apple iPhone Iphone 17 Promax', 'pin tụt nhanh', 'pending', '', '2026-04-09', '0000-00-00', '2026-04-14 07:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `company_series` varchar(100) NOT NULL,
  `model_no` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_incidents`
--

CREATE TABLE `sales_incidents` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `detail` text NOT NULL,
  `priority` varchar(30) NOT NULL DEFAULT 'medium',
  `status` varchar(30) NOT NULL DEFAULT 'open',
  `assignee_username` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_team_tasks`
--

CREATE TABLE `sales_team_tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `team_name` varchar(120) NOT NULL,
  `assignee_username` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'open',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(120) NOT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `topic` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'open',
  `assigned_to` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_tasks`
--

CREATE TABLE `system_tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `priority` varchar(30) NOT NULL DEFAULT 'medium',
  `owner_username` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `account_id` int(11) NOT NULL,
  `imei_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`account_id`, `imei_number`, `created_at`) VALUES
(1, 'Vn', '2026-04-09 14:25:39'),
(2, 'Vn', '2026-04-09 22:26:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_username`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`account_id`,`product_imei`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `mobile`
--
ALTER TABLE `mobile`
  ADD PRIMARY KEY (`imei_number`),
  ADD KEY `idx_brand_series_model` (`company_name`,`company_series`,`model_no`);

--
-- Indexes for table `operations_tasks`
--
ALTER TABLE `operations_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentbill`
--
ALTER TABLE `paymentbill`
  ADD PRIMARY KEY (`pb_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_images_imei` (`imei_number`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promo_code`);

--
-- Indexes for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `sales_incidents`
--
ALTER TABLE `sales_incidents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_team_tasks`
--
ALTER TABLE `sales_team_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_tasks`
--
ALTER TABLE `system_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`account_id`,`imei_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `operations_tasks`
--
ALTER TABLE `operations_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `paymentbill`
--
ALTER TABLE `paymentbill`
  MODIFY `pb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_incidents`
--
ALTER TABLE `sales_incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_team_tasks`
--
ALTER TABLE `sales_team_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_tasks`
--
ALTER TABLE `system_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
