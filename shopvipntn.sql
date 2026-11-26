-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2026 at 01:54 PM
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
-- Database: `shopvip`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `id` int(11) NOT NULL,
  `stk` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `chi_nhanh` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `ghichu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`id`, `stk`, `name`, `bank_name`, `chi_nhanh`, `logo`, `ghichu`) VALUES
(2, '11996371', 'ACB', 'VO TAN NAM', NULL, 'https://i.imgur.com/VnQTSQ4.gif', 'Vui lòng nhập đúng nội dung khi chuyển khoản');

-- --------------------------------------------------------

--
-- Table structure for table `bank_auto`
--

CREATE TABLE `bank_auto` (
  `id` int(11) NOT NULL,
  `tid` varchar(64) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` int(11) DEFAULT 0,
  `cusum_balance` int(11) DEFAULT 0,
  `time` datetime DEFAULT NULL,
  `bank_sub_acc_id` varchar(64) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `deletedate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `bank_auto`
--

INSERT INTO `bank_auto` (`id`, `tid`, `description`, `amount`, `cusum_balance`, `time`, `bank_sub_acc_id`, `username`, `deletedate`) VALUES
(1, '7732a941dd62acd8b5b3b04b39c524c9', 'naptien 8 GD 6317SHBAP28PGYLJ 131126-17:35:13', 10000, 0, '2026-11-14 09:30:32', NULL, 'admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `code` varchar(32) DEFAULT NULL,
  `username` varchar(32) NOT NULL,
  `loaithe` varchar(32) NOT NULL,
  `menhgia` int(11) NOT NULL,
  `thucnhan` int(11) DEFAULT 0,
  `seri` mediumtext NOT NULL,
  `pin` mediumtext NOT NULL,
  `createdate` datetime NOT NULL,
  `status` varchar(32) NOT NULL,
  `note` mediumtext NOT NULL,
  `deletedate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `code`, `username`, `loaithe`, `menhgia`, `thucnhan`, `seri`, `pin`, `createdate`, `status`, `note`, `deletedate`) VALUES
(1, '777903336', 'admin', 'VIETTEL', 50000, 0, '10002626263636', '281615151515199', '2023-11-24 20:09:05', 'thatbai', '', NULL),
(2, '302289172', 'admin', 'VIETTEL', 50000, 0, '10002626263639', '281615151515198', '2023-12-02 18:07:41', 'thatbai', '', '2025/02/11 14:08:21');

-- --------------------------------------------------------

--
-- Table structure for table `dongtien`
--

CREATE TABLE `dongtien` (
  `id` int(11) NOT NULL,
  `sotientruoc` int(11) DEFAULT NULL,
  `sotienthaydoi` int(11) DEFAULT NULL,
  `sotiensau` int(11) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `noidung` text DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_bank_payments`
--

CREATE TABLE `ecom_bank_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `provider` varchar(40) NOT NULL DEFAULT 'bank',
  `transaction_id` varchar(128) NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('accepted','mismatch','duplicate_review') NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_bank_payments`
--

INSERT INTO `ecom_bank_payments` (`id`, `provider`, `transaction_id`, `order_id`, `amount`, `description`, `status`, `created_at`) VALUES
(1, 'bank', 'b275007a889fec72f1895c36a41c1c23', 3, 5000.00, 'EC6B788EE46165C5538FBA68EA GD 6318SHBAP2F2XJ9D 141126-15:31:56', 'accepted', '2026-11-14 09:32:07'),
(2, 'bank', '273177afeb921e8d57d37358a58f38ec', 4, 5000.00, 'ECE0B1F0DD142253A468F3203E GD 6320SHBAP2F1Y8RC 161126-13:47:22', 'accepted', '2026-11-16 07:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_categories`
--

CREATE TABLE `ecom_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','hidden') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_categories`
--

INSERT INTO `ecom_categories` (`id`, `title`, `slug`, `image`, `sort_order`, `status`, `created_at`) VALUES
(1, 'TV', 'cat-4d004673e6', '', 0, 'hidden', '2026-11-14 04:56:38'),
(2, 'ASUS', 'laptop-asus', '', 1, 'active', '2026-11-26 17:57:57'),
(3, 'HP', 'laptop-hp', '', 2, 'active', '2026-11-26 17:57:57'),
(4, 'Dell', 'laptop-dell', '', 3, 'active', '2026-11-26 17:57:57'),
(5, 'MacBook', 'laptop-macbook', '', 4, 'active', '2026-11-26 17:57:57'),
(6, 'Lenovo', 'laptop-lenovo', '', 5, 'active', '2026-11-26 17:57:57'),
(7, 'Acer', 'laptop-acer', '', 6, 'active', '2026-11-26 17:57:57'),
(8, 'MSI', 'laptop-msi', '', 7, 'active', '2026-11-26 17:57:57'),
(9, 'Gigabyte', 'laptop-gigabyte', '', 8, 'active', '2026-11-26 17:57:57');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_commissions`
--

CREATE TABLE `ecom_commissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `commission_key` varchar(100) NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_item_id` int(10) UNSIGNED DEFAULT NULL,
  `ctv_user_id` int(11) NOT NULL,
  `kind` enum('referral','seller') NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `status` enum('pending','approved','paid','reversed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_orders`
--

CREATE TABLE `ecom_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_no` varchar(32) NOT NULL,
  `customer_user_id` int(11) NOT NULL,
  `referrer_user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(190) NOT NULL,
  `customer_email` varchar(190) DEFAULT NULL,
  `customer_phone` varchar(40) NOT NULL,
  `shipping_address` varchar(500) NOT NULL,
  `shipping_city` varchar(120) NOT NULL,
  `shipping_note` text DEFAULT NULL,
  `shipping_carrier` varchar(120) DEFAULT NULL,
  `tracking_no` varchar(120) DEFAULT NULL,
  `refund_reference` varchar(190) DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `shipping_fee` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL,
  `payment_method` enum('bank_transfer','cod') NOT NULL DEFAULT 'bank_transfer',
  `status` enum('pending_payment','paid','processing','shipped','completed','cancelled','refunded','payment_review') NOT NULL DEFAULT 'pending_payment',
  `payment_status` enum('unpaid','paid','mismatch','refunded') NOT NULL DEFAULT 'unpaid',
  `created_at` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_orders`
--

INSERT INTO `ecom_orders` (`id`, `order_no`, `customer_user_id`, `referrer_user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_note`, `shipping_carrier`, `tracking_no`, `refund_reference`, `subtotal`, `shipping_fee`, `total`, `payment_method`, `status`, `payment_status`, `created_at`, `paid_at`, `updated_at`) VALUES
(1, 'ECAA71BE5B8E2E6C104A5BF966', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'trt', 'Hà Nội', '', '', '', '', 10000.00, 0.00, 10000.00, 'cod', 'shipped', 'paid', '2026-11-14 05:03:45', '2026-11-14 09:05:24', '2026-11-14 09:16:52'),
(2, 'ECB77DFD6DC25AFA3F0C3541CE', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'thht', 'Cần Thơ', 'rett', '', '', '', 5000.00, 0.00, 5000.00, 'bank_transfer', 'completed', 'unpaid', '2026-11-14 05:04:10', NULL, '2026-11-14 09:16:31'),
(3, 'EC6B788EE46165C5538FBA68EA', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', '21', 'Đà Nẵng', 'ok', '', '', '', 5000.00, 0.00, 5000.00, 'bank_transfer', 'completed', 'paid', '2026-11-14 09:31:18', '2026-11-14 09:32:07', '2026-11-14 09:33:49'),
(4, 'ECE0B1F0DD142253A468F3203E', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'nbnn', 'Điện Biên', '', '', '', '', 5000.00, 0.00, 5000.00, 'bank_transfer', 'completed', 'paid', '2026-11-16 07:44:15', '2026-11-16 07:47:34', '2026-11-16 07:49:03'),
(5, 'EC72C1D46439725CA6D83C3824', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'xcvv', 'Hưng Yên', 'vvc', '', '', '', 15000.00, 0.00, 15000.00, 'cod', 'completed', 'paid', '2026-11-16 11:13:48', '2026-11-26 12:14:56', '2026-11-26 12:14:56'),
(6, 'EC7A9FCC7E56C67A00626436B2', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'gg', 'Bắc Ninh', '', NULL, NULL, NULL, 41080000.00, 0.00, 41080000.00, 'bank_transfer', 'pending_payment', 'unpaid', '2026-11-26 12:16:21', NULL, '2026-11-26 12:16:21'),
(7, 'EC692FF4292E80E1346FDFF802', 10, 0, 'nam1234', 'admin123@gmail.com', '+84324667788', 'đ', 'Đồng Tháp', '', NULL, NULL, NULL, 35990000.00, 0.00, 35990000.00, 'cod', 'processing', 'unpaid', '2026-11-26 12:50:45', NULL, '2026-11-26 12:50:45'),
(8, 'EC9683F73AF53C16545015786B', 10, 0, 'nam1234', 'admin123@gmail.com', '+84324667788', 'ff', 'Đồng Tháp', '', NULL, NULL, NULL, 27490000.00, 0.00, 27490000.00, 'bank_transfer', 'pending_payment', 'unpaid', '2026-11-26 12:51:09', NULL, '2026-11-26 12:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_order_items`
--

CREATE TABLE `ecom_order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `seller_user_id` int(11) DEFAULT NULL,
  `product_name` varchar(190) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `unit_price` decimal(14,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `line_total` decimal(14,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_order_items`
--

INSERT INTO `ecom_order_items` (`id`, `order_id`, `product_id`, `seller_user_id`, `product_name`, `sku`, `unit_price`, `quantity`, `line_total`) VALUES
(1, 1, 1, 0, 'fsf', 'FGDGF', 5000.00, 2, 10000.00),
(2, 2, 1, 0, 'fsf', 'FGDGF', 5000.00, 1, 5000.00),
(3, 3, 1, 0, 'fsf', 'FGDGF', 5000.00, 1, 5000.00),
(4, 4, 1, 0, 'fsf', 'FGDGF', 5000.00, 1, 5000.00),
(5, 5, 1, 0, 'fsf', 'FGDGF', 5000.00, 3, 15000.00),
(6, 6, 2, 0, 'Asus Vivobook 14 X1404VA Core 3 100U (EB2385W)', 'TGDD-ASUS-563446DE22BF', 15590000.00, 1, 15590000.00),
(7, 6, 3, 0, 'Asus Vivobook S 16 S3607VA Core 5 210H (RP155W)', 'TGDD-ASUS-BFFA16521758', 25490000.00, 1, 25490000.00),
(8, 7, 4, 0, 'Asus Zenbook 14 UM3406GA R7 AI 445 (QD073WS)', 'TGDD-ASUS-56FE7E18CF00', 35990000.00, 1, 35990000.00),
(9, 8, 7, 0, 'Asus TUF Gaming A15 FA506NCQ R7 170 (HN005W)', 'TGDD-ASUS-FF0A3DAE5957', 27490000.00, 1, 27490000.00);

-- --------------------------------------------------------

--
-- Table structure for table `ecom_payouts`
--

CREATE TABLE `ecom_payouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ctv_user_id` int(11) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `status` enum('requested','approved','paid','rejected') NOT NULL DEFAULT 'requested',
  `payout_reference` varchar(190) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_payout_items`
--

CREATE TABLE `ecom_payout_items` (
  `payout_id` int(10) UNSIGNED NOT NULL,
  `commission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_products`
--

CREATE TABLE `ecom_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `seller_user_id` int(11) DEFAULT NULL,
  `name` varchar(190) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `brand` varchar(120) DEFAULT NULL,
  `model` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `warranty_months` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `approval_status` enum('pending','approved','rejected','hidden') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_products`
--

INSERT INTO `ecom_products` (`id`, `category_id`, `seller_user_id`, `name`, `sku`, `brand`, `model`, `description`, `image_url`, `price`, `stock`, `warranty_months`, `approval_status`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'fsf', 'FGDGF', 'hghgh', 'ghh', 'hghgh', 'https://cdn.upanhlaylink.com/i/s5GZH6Iz.png', 5000.00, 992, 12, 'approved', '2026-11-14 04:59:14', '2026-11-16 11:13:48'),
(2, 2, 0, 'Asus Vivobook 14 X1404VA Core 3 100U (EB2385W)', 'TGDD-ASUS-563446DE22BF', 'ASUS', 'Asus Vivobook 14 X1404VA Core 3 100U (EB2385W)', 'Cấu hình tham khảo: 14 inch, RAM 8 GB, SSD 256 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042104187-thumb530-20261116-a24d9f3a.jpg', 15590000.00, 0, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(3, 2, 0, 'Asus Vivobook S 16 S3607VA Core 5 210H (RP155W)', 'TGDD-ASUS-BFFA16521758', 'ASUS', 'Asus Vivobook S 16 S3607VA Core 5 210H (RP155W)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004028-thumb530-20261116-f239bf4f.jpg', 25490000.00, 0, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(4, 2, 0, 'Asus Zenbook 14 UM3406GA R7 AI 445 (QD073WS)', 'TGDD-ASUS-56FE7E18CF00', 'ASUS', 'Asus Zenbook 14 UM3406GA R7 AI 445 (QD073WS)', 'Cấu hình tham khảo: 14 inch, RAM 32 GB, SSD 1 TB, Copilot+ PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003674-thumb530-20261116-e7cc8daf.jpg', 35990000.00, 0, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 12:50:45'),
(5, 2, 0, 'Asus Vivobook S14 S3407VA Core 5 210H (LY146W)', 'TGDD-ASUS-A1054F0F9623', 'ASUS', 'Asus Vivobook S14 S3407VA Core 5 210H (LY146W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003498-thumb530-20261116-443363b5.jpg', 21890000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(6, 2, 0, 'Asus Zenbook 14 UX3405CA Ultra 7 255H (ST629W)', 'TGDD-ASUS-973C12634DE2', 'ASUS', 'Asus Zenbook 14 UX3405CA Ultra 7 255H (ST629W)', 'Cấu hình tham khảo: 14 inch, RAM 32 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003813-thumb530-20261116-636b1cff.jpg', 35590000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(7, 2, 0, 'Asus TUF Gaming A15 FA506NCQ R7 170 (HN005W)', 'TGDD-ASUS-FF0A3DAE5957', 'ASUS', 'Asus TUF Gaming A15 FA506NCQ R7 170 (HN005W)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 4GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003670-thumb530-20261116-1e813457.jpg', 27490000.00, 0, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 12:51:09'),
(8, 2, 0, 'Asus Vivobook 15 X1504VA Core 7 150U (BQ295W)', 'TGDD-ASUS-989B81AF59BE', 'ASUS', 'Asus Vivobook 15 X1504VA Core 7 150U (BQ295W)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003556-thumb530-20261116-7330455a.jpg', 23190000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(9, 2, 0, 'Asus Vivobook Go 15 E1504FA R5 40 (BQ374W)', 'TGDD-ASUS-6814AFD206A0', 'ASUS', 'Asus Vivobook Go 15 E1504FA R5 40 (BQ374W)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003669-thumb530-20261116-71255237.jpg', 17990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(10, 2, 0, 'Asus Zenbook 14 UX3405CA Ultra 5 225H (ST1713W)', 'TGDD-ASUS-5DBA4973C70A', 'ASUS', 'Asus Zenbook 14 UX3405CA Ultra 5 225H (ST1713W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104178-thumb530-20261116-18ae544b.jpg', 32990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(11, 2, 0, 'Asus Vivobook Go 15 E1504FA R5 40 (BQ350W)', 'TGDD-ASUS-D8A6A4759AC6', 'ASUS', 'Asus Vivobook Go 15 E1504FA R5 40 (BQ350W)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003656-thumb530-20261116-0ec7e1e0.jpg', 21590000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(12, 2, 0, 'Asus Vivobook 14 X1407CA Ultra 5 225H (LY008W)', 'TGDD-ASUS-9ED69EC8D75F', 'ASUS', 'Asus Vivobook 14 X1407CA Ultra 5 225H (LY008W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003660-thumb530-20261116-0db1c399.jpg', 23990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(13, 2, 0, 'Asus TUF Gaming A15 FA506NCG R7 155 (HN396W)', 'TGDD-ASUS-B3BD5E8E62FD', 'ASUS', 'Asus TUF Gaming A15 FA506NCG R7 155 (HN396W)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 4GB, RAM 8 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004093-thumb530-20261116-1f045722.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(14, 2, 0, 'Asus VivoBook Go 14 E1404FA R5 40 (EB945W)', 'TGDD-ASUS-BEE79541AE8E', 'ASUS', 'Asus VivoBook Go 14 E1404FA R5 40 (EB945W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003808-thumb530-20261116-5c5e4158.jpg', 20490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(15, 2, 0, 'Asus Vivobook S14 S3407CA Ultra 5 225H (LY095WS)', 'TGDD-ASUS-CE52788F13CD', 'ASUS', 'Asus Vivobook S14 S3407CA Ultra 5 225H (LY095WS)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003209-thumb530-20261116-d2cb89c8.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(16, 2, 0, 'Asus VivoBook 16 X1607CA Ultra 5 225H (MB980W)', 'TGDD-ASUS-97BA666220CC', 'ASUS', 'Asus VivoBook 16 X1607CA Ultra 5 225H (MB980W)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003700-thumb530-20261116-b70168be.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(17, 2, 0, 'Asus ZenBook 14 UX3405CA Ultra 5 225H (ST628W)', 'TGDD-ASUS-0A78DF03D7E7', 'ASUS', 'Asus ZenBook 14 UX3405CA Ultra 5 225H (ST628W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003812-thumb530-20261116-d70ed3b9.jpg', 29590000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(18, 2, 0, 'Asus Vivobook 14 X1404VA Core 5 120U (EB260W)', 'TGDD-ASUS-39C8C2CCC3C6', 'ASUS', 'Asus Vivobook 14 X1404VA Core 5 120U (EB260W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003553-thumb530-20261116-2b2411d2.jpg', 20990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(19, 2, 0, 'Asus Zenbook 14 UX3405CA Ultra 9 285H (ST648W)', 'TGDD-ASUS-406EEC8B09BF', 'ASUS', 'Asus Zenbook 14 UX3405CA Ultra 9 285H (ST648W)', 'Cấu hình tham khảo: 14 inch, RAM 32 GB, SSD 1 TB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003814-thumb530-20261116-636b1cff.jpg', 38590000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(20, 2, 0, 'Asus Vivobook 15 X1504VA Core 5 120U (BQ285W)', 'TGDD-ASUS-91DD80D38B7A', 'ASUS', 'Asus Vivobook 15 X1504VA Core 5 120U (BQ285W)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003555-thumb530-20261116-7330455a.jpg', 20990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(21, 2, 0, 'Asus Zenbook 14 UX3405CA Ultra 5 225H (PZ187WS)', 'TGDD-ASUS-C6A763E47E16', 'ASUS', 'Asus Zenbook 14 UX3405CA Ultra 5 225H (PZ187WS)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003027-thumb530-20261116-676bd9de.jpg', 30890000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(22, 3, 0, 'HP 240R G10 Core 5 120U (C3RU7AT)', 'TGDD-HP-D8C422949221', 'HP', 'HP 240R G10 Core 5 120U (C3RU7AT)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003457-thumb530-20261116-3f664457.jpg', 21290000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(23, 3, 0, 'HP 15 fc0023AU R5 7520U (D0BH1PA)', 'TGDD-HP-2070437A3416', 'HP', 'HP 15 fc0023AU R5 7520U (D0BH1PA)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003571-thumb530-20261116-f21d4179.jpg', 21090000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(24, 3, 0, 'HP OmniBook 5 AI 16 af1048TU Ultra 5 225U (BZ7Q9PA)', 'TGDD-HP-135289A47F5B', 'HP', 'HP OmniBook 5 AI 16 af1048TU Ultra 5 225U (BZ7Q9PA)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003311-thumb530-20261116-556345ab.jpg', 26990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(25, 3, 0, 'HP Gaming VICTUS 15 fa2731TX i5 13420H (B85LNPA)', 'TGDD-HP-FD7998EBE022', 'HP', 'HP Gaming VICTUS 15 fa2731TX i5 13420H (B85LNPA)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003195-thumb530-20261116-db6a1fcd.jpg', 27990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(26, 3, 0, 'HP 245 G10 R5 7530U (B8PF9AT)', 'TGDD-HP-7DE492D115AD', 'HP', 'HP 245 G10 R5 7530U (B8PF9AT)', 'Cấu hình tham khảo: 14 inch, RAM 8 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003180-thumb530-20261116-2c31b7a6.jpg', 18990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(27, 3, 0, 'HP 15 fd2127TU Ultra 5 225U (D72CCPA)', 'TGDD-HP-A458BFC73F8C', 'HP', 'HP 15 fd2127TU Ultra 5 225U (D72CCPA)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003941-thumb530-20261116-9002e6c6.jpg', 24090000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(28, 3, 0, 'HP OmniBook 5 Flip 14 fp0057TU Core 5 120U (BZ7Q6PA)', 'TGDD-HP-E778349A0836', 'HP', 'HP OmniBook 5 Flip 14 fp0057TU Core 5 120U (BZ7Q6PA)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003472-thumb530-20261116-4b665b75.jpg', 26990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(29, 3, 0, 'HP 15 fd1486TU Ultra 5 125H (D0BH3PA)', 'TGDD-HP-0F7C9A35904B', 'HP', 'HP 15 fd1486TU Ultra 5 125H (D0BH3PA)', 'Cấu hình tham khảo: 15.6 inch, RAM 24 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003653-thumb530-20261116-94f875ed.jpg', 21990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(30, 3, 0, 'HP 15 fd2126TU Ultra 5 225U (D72CBPA)', 'TGDD-HP-73636EBA2734', 'HP', 'HP 15 fd2126TU Ultra 5 225U (D72CBPA)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003940-thumb530-20261116-9002e6c6.jpg', 24090000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(31, 3, 0, 'HP 15 fd1487TU Ultra 5 125H (D0BH4PA)', 'TGDD-HP-E31952F7E4F1', 'HP', 'HP 15 fd1487TU Ultra 5 125H (D0BH4PA)', 'Cấu hình tham khảo: 15.6 inch, RAM 24 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003883-thumb530-20261116-f3d44764.jpg', 21990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(32, 3, 0, 'HP Gaming VICTUS 15 fb3115AX R7 7445HS (BX9C9PA)', 'TGDD-HP-AB947BC461D4', 'HP', 'HP Gaming VICTUS 15 fb3115AX R7 7445HS (BX9C9PA)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003329-thumb530-20261116-1f414e34.jpg', 30490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(33, 3, 0, 'HP Pavilion 16 af0055TU Ultra 5 125U (AY8C4PA)', 'TGDD-HP-D3F92E309C66', 'HP', 'HP Pavilion 16 af0055TU Ultra 5 125U (AY8C4PA)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003168-thumb530-20261116-b3e009a3.jpg', 20890000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(34, 3, 0, 'HP Gaming VICTUS 15 fb3116AX R7 7445HS (BX8U4PA)', 'TGDD-HP-3B9009E5472F', 'HP', 'HP Gaming VICTUS 15 fb3116AX R7 7445HS (BX8U4PA)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003328-thumb530-20261116-44e74768.jpg', 27990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(35, 3, 0, 'HP 15 fd1288TU Ultra 7 155H (C2CV7PA)', 'TGDD-HP-52B93A9FC867', 'HP', 'HP 15 fd1288TU Ultra 7 155H (C2CV7PA)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003517-thumb530-20261116-855ad905.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(36, 3, 0, 'HP OmniBook 5 AI 16 af1046TU Ultra 5 225U (BZ7Q8PA)', 'TGDD-HP-40A1DD53BAFF', 'HP', 'HP OmniBook 5 AI 16 af1046TU Ultra 5 225U (BZ7Q8PA)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003310-thumb530-20261116-c9b53a5a.jpg', 27990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(37, 3, 0, 'HP OmniBook 5 Flip 14 fp0055TU Core 7 150U (BZ7Q4PA)', 'TGDD-HP-DA60B1DAD03B', 'HP', 'HP OmniBook 5 Flip 14 fp0055TU Core 7 150U (BZ7Q4PA)', 'Cấu hình tham khảo: 14 inch, RAM 24 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003471-thumb530-20261116-ed14b741.jpg', 31690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(38, 3, 0, 'HP OmniBook 5 16 ag1069AU R5 AI 340 (BZ7T1PA)', 'TGDD-HP-D6E318053B66', 'HP', 'HP OmniBook 5 16 ag1069AU R5 AI 340 (BZ7T1PA)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003477-thumb530-20261116-e758f29a.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(39, 3, 0, 'HP 240R G9 Core 5 120U (C40LGAT)', 'TGDD-HP-B77CFCC9EF96', 'HP', 'HP 240R G9 Core 5 120U (C40LGAT)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003870-thumb530-20261116-88e0bf7c.jpg', 21290000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(40, 3, 0, 'HP OmniBook X Flip 14 fm0088TU Ultra 5 226V (BZ7Q2PA)', 'TGDD-HP-49F072EEDADB', 'HP', 'HP OmniBook X Flip 14 fm0088TU Ultra 5 226V (BZ7Q2PA)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003474-thumb530-20261116-2b57a40f.jpg', 31390000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(41, 3, 0, 'HP OmniBook 5 AI 16 af1054TU Ultra 7 255U (C1MN8PA)', 'TGDD-HP-ECC3F09B527F', 'HP', 'HP OmniBook 5 AI 16 af1054TU Ultra 7 255U (C1MN8PA)', 'Cấu hình tham khảo: 16 inch, RAM 32 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003468-thumb530-20261116-ddf64ae5.jpg', 32390000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(42, 4, 0, 'Dell 15 DC15255 R5 7530U (DC5R5973W1-2Y)', 'TGDD-DELL-4CDC4251635C', 'Dell', 'Dell 15 DC15255 R5 7530U (DC5R5973W1-2Y)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003992-thumb530-20261116-326bb4f6.jpg', 23990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(43, 4, 0, 'Dell 15 DC15250 i5 1334U (DC5I5897W1)', 'TGDD-DELL-5ACA5C4231C8', 'Dell', 'Dell 15 DC15250 i5 1334U (DC5I5897W1)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003649-thumb530-20261116-d3c5e4c6.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(44, 4, 0, 'Dell 15 DC15250 i5 1334U (71092479)', 'TGDD-DELL-B6637B633828', 'Dell', 'Dell 15 DC15250 i5 1334U (71092479)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003833-thumb530-20261116-d3c5e4c6.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(45, 4, 0, 'Dell 15 DC15250 i5 1334U (CPH99)', 'TGDD-DELL-71A079E67B7F', 'Dell', 'Dell 15 DC15250 i5 1334U (CPH99)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003463-thumb530-20261116-5af6a46b.jpg', 22490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(46, 4, 0, 'Dell 14 DC14250 Core 5 120U (DC4C5386W)', 'TGDD-DELL-07F13A73E8F0', 'Dell', 'Dell 14 DC14250 Core 5 120U (DC4C5386W)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003562-thumb530-20261116-3e6c6942.jpg', 27290000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(47, 4, 0, 'Dell 15 DC15250 i5 1334U (DC15250-i5U165W11SLU-27)', 'TGDD-DELL-95434DBE1D5B', 'Dell', 'Dell 15 DC15250 i5 1334U (DC15250-i5U165W11SLU-27)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003857-thumb530-20261116-ca43e612.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(48, 4, 0, 'Dell Pro 14 Essential PV14250 Core 5 120U (PV14250-120U-16512W-BL)', 'TGDD-DELL-23D061FEEE0C', 'Dell', 'Dell Pro 14 Essential PV14250 Core 5 120U (PV14250-120U-16512W-BL)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003648-thumb530-20261116-fdb52ec0.jpg', 27490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(49, 4, 0, 'Dell 15 DC15250 i7 1355U (DC5I7748W1)', 'TGDD-DELL-CFB10795C331', 'Dell', 'Dell 15 DC15250 i7 1355U (DC5I7748W1)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003306-thumb530-20261116-01343904.jpg', 25490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(50, 4, 0, 'Dell 15 DC15250 i5 1334U (DC5I5357W1)', 'TGDD-DELL-453CB46E1C37', 'Dell', 'Dell 15 DC15250 i5 1334U (DC5I5357W1)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003305-thumb530-20261116-01343904.jpg', 22490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(51, 4, 0, 'Dell Inspiron 14 5441 X1P 64 100 (5MNK1)', 'TGDD-DELL-0D8F55E41728', 'Dell', 'Dell Inspiron 14 5441 X1P 64 100 (5MNK1)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003277-thumb530-20261116-97f08642.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(52, 4, 0, 'Dell 14 DC14250 Core 3 100U (DC14250-C3U085W11SLU-27)', 'TGDD-DELL-BEDA085435D6', 'Dell', 'Dell 14 DC14250 Core 3 100U (DC14250-C3U085W11SLU-27)', 'Cấu hình tham khảo: 14 inch, RAM 8 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003929-thumb530-20261116-8eede56f.jpg', 18990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(53, 4, 0, 'Dell 15 DC15250 i7 1355U (71084747)', 'TGDD-DELL-9B9C0DA5439F', 'Dell', 'Dell 15 DC15250 i7 1355U (71084747)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003561-thumb530-20261116-d3c5e4c6.jpg', 25490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(54, 4, 0, 'Dell 15 DC15255 R7 7730U (X9YM42)', 'TGDD-DELL-5E646A4B66CC', 'Dell', 'Dell 15 DC15255 R7 7730U (X9YM42)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004057-thumb530-20261116-091073a0.jpg', 28490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(55, 4, 0, 'Dell 15 DC15250 i7 1355U (DC5I7952W1)', 'TGDD-DELL-05136B054D7B', 'Dell', 'Dell 15 DC15250 i7 1355U (DC5I7952W1)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003650-thumb530-20261116-d3c5e4c6.jpg', 25490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(56, 4, 0, 'Dell 15 DC15250 i7 1355U (CPH991)', 'TGDD-DELL-59FF9A41FDBC', 'Dell', 'Dell 15 DC15250 i7 1355U (CPH991)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003644-thumb530-20261116-fbd73eb7.jpg', 27490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(57, 4, 0, 'Dell 15 DC15250 i5 1334U (71084746)', 'TGDD-DELL-F0A39F7C27C8', 'Dell', 'Dell 15 DC15250 i5 1334U (71084746)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003560-thumb530-20261116-d3c5e4c6.jpg', 22490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(58, 4, 0, 'Dell 15 DC15250 i7 1355U (DC15250-i7U161W11SLU-5)', 'TGDD-DELL-8258F5069F59', 'Dell', 'Dell 15 DC15250 i7 1355U (DC15250-i7U161W11SLU-5)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003667-thumb530-20261116-e2bd72b1.jpg', 26990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(59, 4, 0, 'Dell 15 DC15250 Core 3 100U (71100520)', 'TGDD-DELL-EF737B79F262', 'Dell', 'Dell 15 DC15250 Core 3 100U (71100520)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003994-thumb530-20261116-326bb4f6.jpg', 20990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(60, 4, 0, 'Dell 15 DC15250 i7 1355U (DC15250-i7U161W11SLU)', 'TGDD-DELL-4E336508F632', 'Dell', 'Dell 15 DC15250 i7 1355U (DC15250-i7U161W11SLU)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003444-thumb530-20261116-e2bd72b1.jpg', 27490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(61, 4, 0, 'Dell 14 DC14250 Core 5 120U (DC4C5375W1-2Y)', 'TGDD-DELL-2B51DE8A9C6B', 'Dell', 'Dell 14 DC14250 Core 5 120U (DC4C5375W1-2Y)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003993-thumb530-20261116-f40f4f5e.jpg', 29370000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(62, 5, 0, 'MacBook Neo 13 inch A18 Pro 8GB/256GB', 'TGDD-MAC-BFBD28FA1959', 'MacBook', 'MacBook Neo 13 inch A18 Pro 8GB/256GB', 'Cấu hình tham khảo: 13 inch, RAM 8 GB, SSD 256 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003779-thumb530-20261116-038be3dd.jpg', 19190000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(63, 5, 0, 'MacBook Air 13 inch M5 16GB/512GB/8GPU 30W', 'TGDD-MAC-5EC1B11CF650', 'MacBook', 'MacBook Air 13 inch M5 16GB/512GB/8GPU 30W', 'Cấu hình tham khảo: 13.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003702-thumb530-20261116-6aff52da.jpg', 33690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(64, 5, 0, 'MacBook Air 15 inch M5 16GB/512GB 35W', 'TGDD-MAC-514FD74EC33D', 'MacBook', 'MacBook Air 15 inch M5 16GB/512GB 35W', 'Cấu hình tham khảo: 15.3 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003733-thumb530-20261116-a4a7af57.jpg', 39490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(65, 5, 0, 'MacBook Pro 14 inch M5 Max 36GB/2TB', 'TGDD-MAC-ABD8CE1199D4', 'MacBook', 'MacBook Pro 14 inch M5 Max 36GB/2TB', 'Cấu hình tham khảo: 14.2 inch, RAM 36 GB, SSD 2 TB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003769-thumb530-20261116-61b973ed.jpg', 111190000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(66, 5, 0, 'MacBook Pro 16 inch M5 Pro 24GB/1TB', 'TGDD-MAC-4133EF19CEB3', 'MacBook', 'MacBook Pro 16 inch M5 Pro 24GB/1TB', 'Cấu hình tham khảo: 16.2 inch, RAM 24 GB, SSD 1 TB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003772-thumb530-20261116-37614471.jpg', 79690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(67, 5, 0, 'Laptop MacBook Pro 14 inch M5 Pro 24GB/1TB 70W', 'TGDD-MAC-F83F63F26A01', 'MacBook', 'Laptop MacBook Pro 14 inch M5 Pro 24GB/1TB 70W', 'Cấu hình tham khảo: 14.2 inch, RAM 24 GB, SSD 1 TB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/Products/Images/44/363488/macbook-pro-14-inch-m5-pro-24gb-1tb-bac-1.jpg', 67190000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(68, 5, 0, 'MacBook Pro 14 inch M5 16GB/1TB', 'TGDD-MAC-C4E5234AFFBC', 'MacBook', 'MacBook Pro 14 inch M5 16GB/1TB', 'Cấu hình tham khảo: 14.2 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003482-thumb530-20261116-37614471.jpg', 53190000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(69, 5, 0, 'MacBook Pro 14 inch M5 24GB/1TB', 'TGDD-MAC-62738E780F18', 'MacBook', 'MacBook Pro 14 inch M5 24GB/1TB', 'Cấu hình tham khảo: 14.2 inch, RAM 24 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003484-thumb530-20261116-37614471.jpg', 58490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(70, 5, 0, 'MacBook Air 13 inch M5 24GB/1TB 35W', 'TGDD-MAC-BB5A5211A7F5', 'MacBook', 'MacBook Air 13 inch M5 24GB/1TB 35W', 'Cấu hình tham khảo: 13.6 inch, RAM 24 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/Products/Images/44/363476/macbook-air-13-inch-m5-24gb-1tb-xanh-da-troi.jpg', 45690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(71, 5, 0, 'MacBook Air 15 inch M5 24GB/512GB 70W', 'TGDD-MAC-5D743186A615', 'MacBook', 'MacBook Air 15 inch M5 24GB/512GB 70W', 'Cấu hình tham khảo: 15.3 inch, RAM 24 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/Products/Images/44/363511/macbook-air-15-inch-m5-24gb-512gb-70w-vang-1.jpg', 45690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(72, 5, 0, 'Macbook Air 13 inch M5 24GB/512GB 70W', 'TGDD-MAC-F627E4C66155', 'MacBook', 'Macbook Air 13 inch M5 24GB/512GB 70W', 'Cấu hình tham khảo: 13.6 inch, RAM 24 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/Products/Images/44/363504/macbook-air-13-inch-m5-24gb-512gb-70w-xanh-thumb.png', 39990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(73, 5, 0, 'Macbook Pro 16 inch M5 Max 36GB/2TB', 'TGDD-MAC-1D45CD18D5F4', 'MacBook', 'Macbook Pro 16 inch M5 Max 36GB/2TB', 'Cấu hình tham khảo: 16.2 inch, RAM 36 GB, SSD 2 TB.', 'https://cdn.tgdd.vn/Products/Images/44/363493/macbook-pro-16-inch-m5-max-36gb-2tb-den-1.jpg', 117690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(74, 5, 0, 'Macbook Pro 16 inch PROLED M5 Pro 24GB/1TB', 'TGDD-MAC-CFA6060D50A7', 'MacBook', 'Macbook Pro 16 inch PROLED M5 Pro 24GB/1TB', 'Cấu hình tham khảo: 16.3 inch, RAM 24 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104225-thumb530-20261116-05962d44.jpg', 92990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(75, 5, 0, 'Macbook Pro 14 inch PROLED M5 Max 36GB/2TB', 'TGDD-MAC-6CBF94DBAE9D', 'MacBook', 'Macbook Pro 14 inch PROLED M5 Max 36GB/2TB', 'Cấu hình tham khảo: 14.2 inch, RAM 36 GB, SSD 2 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104223-thumb530-20261116-ad3dc7fb.jpg', 123990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(76, 5, 0, 'Macbook Pro 14 inch PROLED M5 Pro 24GB/1TB', 'TGDD-MAC-367C8B2A95C7', 'MacBook', 'Macbook Pro 14 inch PROLED M5 Pro 24GB/1TB', 'Cấu hình tham khảo: 14.2 inch, RAM 24 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104215-thumb530-20261116-ad3dc7fb.jpg', 79990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(77, 5, 0, 'MacBook Pro 14 inch M6 16GB/1TB', 'TGDD-MAC-D5FC7703084C', 'MacBook', 'MacBook Pro 14 inch M6 16GB/1TB', 'Cấu hình tham khảo: 14.2 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104203-thumb530-20261116-3efc80b4.jpg', 54990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(78, 5, 0, 'MacBook Air 15 inch M5 16GB/1TB 35W', 'TGDD-MAC-1F1422E7CB5A', 'MacBook', 'MacBook Air 15 inch M5 16GB/1TB 35W', 'Cấu hình tham khảo: 15.3 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003742-thumb530-20261116-6aff52da.jpg', 46490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(79, 6, 0, 'Lenovo IdeaPad Slim 3 15IPH11 Ultra 5 322 (83UR00A4VN)', 'TGDD-LENOVO-853BA3444716', 'Lenovo', 'Lenovo IdeaPad Slim 3 15IPH11 Ultra 5 322 (83UR00A4VN)', 'Cấu hình tham khảo: 15.3 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003922-thumb530-20261116-a30e5b79.jpg', 24990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(80, 6, 0, 'Lenovo Gaming LOQ 15ARP10 R7 170 (83S000CNVN)', 'TGDD-LENOVO-D2A291F8BC0B', 'Lenovo', 'Lenovo Gaming LOQ 15ARP10 R7 170 (83S000CNVN)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdnv2.tgdd.vn/mwg-static/tgdd/Products/Images/44/363903/lenovo-gaming-loq-15arp10-r7-170-83s000cnvn-thumb-639088378609730153-600x600.jpg', 28990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(81, 6, 0, 'Lenovo Ideapad Slim 3 15IWC11 Core 5 320 (83RR00CVVN)', 'TGDD-LENOVO-8096EB3B004D', 'Lenovo', 'Lenovo Ideapad Slim 3 15IWC11 Core 5 320 (83RR00CVVN)', 'Cấu hình tham khảo: 15.3 inch, RAM 8 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003980-thumb530-20261116-7e2eeeed.jpg', 18990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(82, 6, 0, 'Lenovo IdeaPad Slim 3 15IWC11 Core 5 320 (83RR00AAVN)', 'TGDD-LENOVO-D4793628D75C', 'Lenovo', 'Lenovo IdeaPad Slim 3 15IWC11 Core 5 320 (83RR00AAVN)', 'Cấu hình tham khảo: 15.3 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003979-thumb530-20261116-7e2eeeed.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(83, 6, 0, 'Lenovo IdeaPad Slim 3 14IWC11 Core 5 320 (83RQ002PVN)', 'TGDD-LENOVO-F0A09D5170C9', 'Lenovo', 'Lenovo IdeaPad Slim 3 14IWC11 Core 5 320 (83RQ002PVN)', 'Cấu hình tham khảo: 14 inch, RAM 8 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003977-thumb530-20261116-8eaa4b70.jpg', 18990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(84, 6, 0, 'Lenovo Gaming LOQ 15IRX9 i5 13450HX (83DV003CVN)', 'TGDD-LENOVO-25A8E0410190', 'Lenovo', 'Lenovo Gaming LOQ 15IRX9 i5 13450HX (83DV003CVN)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003141-thumb530-20261116-a9264b6f.jpg', 34990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(85, 6, 0, 'Lenovo Gaming LOQ 15IRX9 i5 13450HX (83DV01ANVN)', 'TGDD-LENOVO-8C8D9F3926BC', 'Lenovo', 'Lenovo Gaming LOQ 15IRX9 i5 13450HX (83DV01ANVN)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 6GB, RAM 16 GB, SSD 1 TB, RTX AI PC.', 'https://cdnv2.tgdd.vn/mwg-static/tgdd/Products/Images/44/342524/lenovo-gaming-loq-15irx9-i5-83dv01anvn-thumb-638913705000350912-600x600.jpg', 29290000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(86, 6, 0, 'Lenovo IdeaPad Slim 3 14IPH11 Ultra 5 322 (83UQ003NVN)', 'TGDD-LENOVO-CA0F453FB5F2', 'Lenovo', 'Lenovo IdeaPad Slim 3 14IPH11 Ultra 5 322 (83UQ003NVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003945-thumb530-20261116-73775a1f.jpg', 24990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(87, 6, 0, 'Lenovo Ideapad Slim 5 OLED 14AKP10 R5 330 (83HX00B2VN)', 'TGDD-LENOVO-132021B7F399', 'Lenovo', 'Lenovo Ideapad Slim 5 OLED 14AKP10 R5 330 (83HX00B2VN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 1 TB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003531-thumb530-20261116-b8524c8a.jpg', 28990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(88, 6, 0, 'Lenovo IdeaPad Slim 5 OLED 14IPH11 Ultra 5 322 (83S5000DVN)', 'TGDD-LENOVO-1DD1F579229C', 'Lenovo', 'Lenovo IdeaPad Slim 5 OLED 14IPH11 Ultra 5 322 (83S5000DVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003906-thumb530-20261116-49cfa790.jpg', 28990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(89, 6, 0, 'Lenovo Gaming LOQ 15IRX9 i7 13645HX (83DV01H2VN)', 'TGDD-LENOVO-2CAECA2E23D8', 'Lenovo', 'Lenovo Gaming LOQ 15IRX9 i7 13645HX (83DV01H2VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 1 TB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003914-thumb530-20261116-f3513d62.jpg', 35990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(90, 6, 0, 'Lenovo IdeaPad Slim 3 15IWC11 Core 3 304 (83RR00A8VN)', 'TGDD-LENOVO-4889433CAF9D', 'Lenovo', 'Lenovo IdeaPad Slim 3 15IWC11 Core 3 304 (83RR00A8VN)', 'Cấu hình tham khảo: 15.3 inch, RAM 8 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004039-thumb530-20261116-455381d8.jpg', 16990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(91, 6, 0, 'Lenovo IdeaPad Slim 3 14IWC11 Core 5 320 (83RQ002NVN)', 'TGDD-LENOVO-C116EA2B7876', 'Lenovo', 'Lenovo IdeaPad Slim 3 14IWC11 Core 5 320 (83RQ002NVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003976-thumb530-20261116-8eaa4b70.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(92, 6, 0, 'Lenovo Ideapad Slim 5 OLED 14AGP11 R7 445 (83S1003FVN)', 'TGDD-LENOVO-E239B16DE026', 'Lenovo', 'Lenovo Ideapad Slim 5 OLED 14AGP11 R7 445 (83S1003FVN)', 'Cấu hình tham khảo: 14 inch, RAM 32 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003788-thumb530-20261116-8cc45cdc.jpg', 35990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(93, 6, 0, 'Lenovo Gaming LOQ 15IRX9 i7 13650HX (83DV01ALVN)', 'TGDD-LENOVO-14F026208539', 'Lenovo', 'Lenovo Gaming LOQ 15IRX9 i7 13650HX (83DV01ALVN)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003429-thumb530-20261116-6b0ee514.jpg', 39990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(94, 6, 0, 'Lenovo ThinkBook 16 G9 IRL i5 13420H (21US008FVN)', 'TGDD-LENOVO-1B81E51D4600', 'Lenovo', 'Lenovo ThinkBook 16 G9 IRL i5 13420H (21US008FVN)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003801-thumb530-20261116-587fbe8e.jpg', 28090000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(95, 6, 0, 'Lenovo ThinkBook 14 Core 7 240H (21SG007VVN)', 'TGDD-LENOVO-3846C56DA5D7', 'Lenovo', 'Lenovo ThinkBook 14 Core 7 240H (21SG007VVN)', 'Cấu hình tham khảo: 14 inch, RAM 32 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003384-thumb530-20261116-b09baab9.jpg', 36590000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(96, 6, 0, 'Lenovo ThinkPad E14 Gen 7 Ultra 5 225U (21SX005NVN)', 'TGDD-LENOVO-798C0496F203', 'Lenovo', 'Lenovo ThinkPad E14 Gen 7 Ultra 5 225U (21SX005NVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003989-thumb530-20261116-782c12a4.jpg', 36990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(97, 6, 0, 'Lenovo V14 G5 i5 13420H (83HD005JVN)', 'TGDD-LENOVO-B537CF8FA7AF', 'Lenovo', 'Lenovo V14 G5 i5 13420H (83HD005JVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003528-thumb530-20261116-8cac62a6.jpg', 24390000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(98, 6, 0, 'Lenovo ThinkBook 14 Gen 8 Ultra 5 135H (21SJ00EAVN)', 'TGDD-LENOVO-FAA07E34B0A2', 'Lenovo', 'Lenovo ThinkBook 14 Gen 8 Ultra 5 135H (21SJ00EAVN)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003797-thumb530-20261116-2ba6bb95.jpg', 32290000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(99, 7, 0, 'Acer Aspire Lite 14 AL14-44P-R0SP R7 7730U (NX.DMCSV.003)', 'TGDD-ACER-3BD7C1035C39', 'Acer', 'Acer Aspire Lite 14 AL14-44P-R0SP R7 7730U (NX.DMCSV.003)', 'Cấu hình tham khảo: 14.5 inch, RAM 8 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003933-thumb530-20261116-fa5d018c.jpg', 19490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(100, 7, 0, 'Acer Aspire Lite 15 AL15-53P-56EC Core 5 120U (NX.DG3SV.002.16G)', 'TGDD-ACER-4C63A6C2D0FB', 'Acer', 'Acer Aspire Lite 15 AL15-53P-56EC Core 5 120U (NX.DG3SV.002.16G)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004012-thumb530-20261116-49bbff66.jpg', 20490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(101, 7, 0, 'Acer Aspire Lite 15 AL15 72P 581V i5 13500H (NX.D4CSV.001)', 'TGDD-ACER-3B04922C325B', 'Acer', 'Acer Aspire Lite 15 AL15 72P 581V i5 13500H (NX.D4CSV.001)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdnv2.tgdd.vn/mwg-static/tgdd/Products/Images/44/343122/acer-aspire-lite-15-al15-72p-581v-i5-13500h-nx-d4csv-001-thumb-638921804601704800-600x600.jpg', 18990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(102, 7, 0, 'Acer Aspire Lite 14 AL14-57P-59HB Core 5 320 (NX.X1XSV.002)', 'TGDD-ACER-78C456F2322A', 'Acer', 'Acer Aspire Lite 14 AL14-57P-59HB Core 5 320 (NX.X1XSV.002)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004123-thumb530-20261116-a0ceceb8.jpg', 22990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(103, 7, 0, 'Acer Aspire Lite 15 AL15-53P-56QH Core 5 120U (NX.DG3SV.001)', 'TGDD-ACER-38C1B87027ED', 'Acer', 'Acer Aspire Lite 15 AL15-53P-56QH Core 5 120U (NX.DG3SV.001)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003935-thumb530-20261116-c2c950dd.jpg', 18490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(104, 7, 0, 'Acer Aspire Go 15 AG15-52P-52WT Ultra 5 115U (NX.JWKSV.001)', 'TGDD-ACER-951E0056303D', 'Acer', 'Acer Aspire Go 15 AG15-52P-52WT Ultra 5 115U (NX.JWKSV.001)', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB, AI Ready.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003891-thumb530-20261116-60c5eeae.jpg', 21990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(105, 7, 0, 'Acer Gaming Aspire 7 A715-59G-59RD Core 5 210H (NH.DXUSV.001)', 'TGDD-ACER-0BF80BE8ECDB', 'Acer', 'Acer Gaming Aspire 7 A715-59G-59RD Core 5 210H (NH.DXUSV.001)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 4GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004011-thumb530-20261116-fbc5228f.jpg', 25990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(106, 7, 0, 'Acer Gaming Nitro ProPanel ANV16-I12-53VH Core 5 205H (NH.U8QSV.001)', 'TGDD-ACER-68FF57D853A3', 'Acer', 'Acer Gaming Nitro ProPanel ANV16-I12-53VH Core 5 205H (NH.U8QSV.001)', 'Cấu hình tham khảo: 16 inch, RTX 3050 4GB, RAM 16 GB, SSD 512 GB.', 'https://cdnv2.tgdd.vn/pim/cdn/images/202611/acer-nitro-propanel-anv16-i12-53vh-core-5-205h-nh-u8qsv-001-thumb151415.jpg', 29990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(107, 7, 0, 'Acer Swift Air 14 SFA14-I31-52BV Core 5 320 (NX.W49SV.002)', 'TGDD-ACER-E43536D05467', 'Acer', 'Acer Swift Air 14 SFA14-I31-52BV Core 5 320 (NX.W49SV.002)', 'Cấu hình tham khảo: 14 inch, RAM 12 GB, SSD 512 GB, AI Ready.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004083-thumb530-20261116-0b30cfd1.jpg', 24990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(108, 7, 0, 'Acer Aspire Lite 15 AL15-410P-R1W8 Ryzen 5 3500U (NX.X0RSV.001)', 'TGDD-ACER-BBEDBB71AB4B', 'Acer', 'Acer Aspire Lite 15 AL15-410P-R1W8 Ryzen 5 3500U (NX.X0RSV.001)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004081-thumb530-20261116-7616c4eb.jpg', 17990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(109, 7, 0, 'Acer Gaming Nitro ProPanel ANV15-52-556L Core 5 210H (NH.QZ9SV.006)', 'TGDD-ACER-9B62F3181EF3', 'Acer', 'Acer Gaming Nitro ProPanel ANV15-52-556L Core 5 210H (NH.QZ9SV.006)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004066-thumb530-20261116-33c12a2b.jpg', 37990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(110, 7, 0, 'Acer Swift Go AI SFG14-75-5264 Ultra 5 226V (NX.JNBSV.001)', 'TGDD-ACER-D207D8CEE133', 'Acer', 'Acer Swift Go AI SFG14-75-5264 Ultra 5 226V (NX.JNBSV.001)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004058-thumb530-20261116-46ca95be.jpg', 27990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(111, 7, 0, 'Acer Aspire Lite 15 AL15-46P-R73C R3 5400U (NX.JXMSV.001)', 'TGDD-ACER-90CAED43F9DC', 'Acer', 'Acer Aspire Lite 15 AL15-46P-R73C R3 5400U (NX.JXMSV.001)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://cdnv2.tgdd.vn/mwg-static/tgdd/Products/Images/44/366086/acer-aspire-lite-15-al15-46p-r73c-r3-5400u-nx-jxmsv-001-thumb-639125386869694783-600x600.jpg', 16990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(112, 7, 0, 'Acer Gaming Nitro ProPanel AN16S 61 R193 R9 AI 365 (NH.QXTSV.001)', 'TGDD-ACER-58D0F5D52152', 'Acer', 'Acer Gaming Nitro ProPanel AN16S 61 R193 R9 AI 365 (NH.QXTSV.001)', 'Cấu hình tham khảo: 16 inch, RTX 5070 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003546-thumb530-20261116-94a26839.jpg', 58990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(113, 7, 0, 'Acer Gaming Nitro ProPanel ANV15 52 72BM i7 13620H (NH.QZ9SV.004)', 'TGDD-ACER-9AFA7416116D', 'Acer', 'Acer Gaming Nitro ProPanel ANV15 52 72BM i7 13620H (NH.QZ9SV.004)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003425-thumb530-20261116-cce86478.jpg', 37490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(114, 7, 0, 'Acer Aspire Lite 16 G2 AL16 52P 76DU i7 1355U (NX.J2SSV.005)', 'TGDD-ACER-F58C6E8A0708', 'Acer', 'Acer Aspire Lite 16 G2 AL16 52P 76DU i7 1355U (NX.J2SSV.005)', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042002979-thumb530-20261116-ba86a131.jpg', 20990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(115, 7, 0, 'Acer Aspire Lite 15 AL15-48P-R86Q R7 5825U (NX.DS1SV.002)', 'TGDD-ACER-D3C6CB9A4140', 'Acer', 'Acer Aspire Lite 15 AL15-48P-R86Q R7 5825U (NX.DS1SV.002)', 'Cấu hình tham khảo: 15.6 inch, RAM 8 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003918-thumb530-20261116-be913282.jpg', 19690000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(116, 7, 0, 'Acer Gaming Nitro ProPanel ANV15 52 59AA i5 13420H (NH.QZ9SV.002)', 'TGDD-ACER-D9BEA402695B', 'Acer', 'Acer Gaming Nitro ProPanel ANV15 52 59AA i5 13420H (NH.QZ9SV.002)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003349-thumb530-20261116-cce86478.jpg', 35990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(117, 7, 0, 'Acer Gaming Nitro ProPanel ANV15-52-78MD Core 7 240H (NH.QZ9SV.007)', 'TGDD-ACER-4EB19ACED68D', 'Acer', 'Acer Gaming Nitro ProPanel ANV15-52-78MD Core 7 240H (NH.QZ9SV.007)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004067-thumb530-20261116-40225d6d.jpg', 41390000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(118, 7, 0, 'Acer Aspire Go 14 AG14-72P-54DF Core 5 120U (NX.JSBSV.009)', 'TGDD-ACER-597612854EC9', 'Acer', 'Acer Aspire Go 14 AG14-72P-54DF Core 5 120U (NX.JSBSV.009)', 'Cấu hình tham khảo: 14 inch, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003696-thumb530-20261116-89b1096e.jpg', 20490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(119, 8, 0, 'MSI Modern 15 F1MG-1264VN Core 5 120U', 'TGDD-MSI-FB3F09F046FA', 'MSI', 'MSI Modern 15 F1MG-1264VN Core 5 120U', 'Cấu hình tham khảo: 15.6 inch, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004015-thumb530-20261116-fe177ede.jpg', 20990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(120, 8, 0, 'MSI Cyborg 15 Black Edition A13VEO i7 13620H (2608VN)', 'TGDD-MSI-579192D2C0E7', 'MSI', 'MSI Cyborg 15 Black Edition A13VEO i7 13620H (2608VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004063-thumb530-20261116-fff03f32.jpg', 31990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(121, 8, 0, 'MSI Gaming Cyborg 15 A13UC i5 13420H (2088VN)', 'TGDD-MSI-C489B5A1E55A', 'MSI', 'MSI Gaming Cyborg 15 A13UC i5 13420H (2088VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 3050 4GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003787-thumb530-20261116-f1a00e1b.jpg', 24990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(122, 8, 0, 'MSI Gaming Katana 15 HX B14WFK i7 14650HX (025VN)', 'TGDD-MSI-4518BCD2CD86', 'MSI', 'MSI Gaming Katana 15 HX B14WFK i7 14650HX (025VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003342-thumb530-20261116-d8e4cf68.jpg', 44990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(123, 8, 0, 'MSI Gaming Katana 15 HX B14WEK i5 14450HX (286VN)', 'TGDD-MSI-BA5C94F48B67', 'MSI', 'MSI Gaming Katana 15 HX B14WEK i5 14450HX (286VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003343-thumb530-20261116-d8e4cf68.jpg', 38990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(124, 8, 0, 'MSI Gaming Cyborg 15 B13WFKG i7 13620H (658VN)', 'TGDD-MSI-61480BEC43E5', 'MSI', 'MSI Gaming Cyborg 15 B13WFKG i7 13620H (658VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5060 8GB, RAM 16 GB, SSD 1 TB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003677-thumb530-20261116-7396ae5d.jpg', 37990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(125, 8, 0, 'MSI Gaming Cyborg 15 Black Edition A13VE i5 13420H (A13VE-2410VN)', 'TGDD-MSI-06300B9D6451', 'MSI', 'MSI Gaming Cyborg 15 Black Edition A13VE i5 13420H (A13VE-2410VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003917-thumb530-20261116-47abab5d.jpg', 28990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51');
INSERT INTO `ecom_products` (`id`, `category_id`, `seller_user_id`, `name`, `sku`, `brand`, `model`, `description`, `image_url`, `price`, `stock`, `warranty_months`, `approval_status`, `created_at`, `updated_at`) VALUES
(126, 8, 0, 'MSI Gaming Katana 15 HX B14WFK i7 14650HX (267VN)', 'TGDD-MSI-021AFC3ECBD9', 'MSI', 'MSI Gaming Katana 15 HX B14WFK i7 14650HX (267VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5060 8GB, RAM 32 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003341-thumb530-20261116-d8e4cf68.jpg', 47990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(127, 8, 0, 'MSI Prestige 14 AI+ Evo C2VMG Ultra 7 258V (020VN)', 'TGDD-MSI-220EEE4C1F44', 'MSI', 'MSI Prestige 14 AI+ Evo C2VMG Ultra 7 258V (020VN)', 'Cấu hình tham khảo: 14.1 inch, RAM 32 GB, SSD 1 TB, Copilot+ PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003047-thumb530-20261116-e320f91f.jpg', 33990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(128, 8, 0, 'MSI Gaming Katana 15 HX B14WGK i7 14650HX (023VN)', 'TGDD-MSI-BED6E0DECB1D', 'MSI', 'MSI Gaming Katana 15 HX B14WGK i7 14650HX (023VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5070 8GB, RAM 16 GB, SSD 1 TB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003535-thumb530-20261116-74d09209.jpg', 47990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(129, 8, 0, 'MSI Gaming Katana 15 HX B14WFK i9 14900HX (294VN)', 'TGDD-MSI-5625DD5982B1', 'MSI', 'MSI Gaming Katana 15 HX B14WFK i9 14900HX (294VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003536-thumb530-20261116-74d09209.jpg', 46990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(130, 8, 0, 'MSI Cyborg 15 Black Edition A13VEO i5 13420H (2610VN)', 'TGDD-MSI-BD7C8E7B37B4', 'MSI', 'MSI Cyborg 15 Black Edition A13VEO i5 13420H (2610VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004064-thumb530-20261116-fff03f32.jpg', 29990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(131, 8, 0, 'MSI Cyborg 15 C13WEO i5 13420H (418VN)', 'TGDD-MSI-5069E609B4AB', 'MSI', 'MSI Cyborg 15 C13WEO i5 13420H (418VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004062-thumb530-20261116-c800345d.jpg', 31990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(132, 8, 0, 'MSI Prestige N16 Flip AI+ C1XMT RTX Spark N1X (016VN)', 'TGDD-MSI-F35F960D6E12', 'MSI', 'MSI Prestige N16 Flip AI+ C1XMT RTX Spark N1X (016VN)', 'Cấu hình tham khảo: 16 inch, RAM 128 GB, SSD 2 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104163-thumb530-20261116-b3695ac7.jpg', 222990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(133, 8, 0, 'MSI Prestige N16 Flip AI+ C1XMT RTX Spark N1X (017VN)', 'TGDD-MSI-2A829E0824B0', 'MSI', 'MSI Prestige N16 Flip AI+ C1XMT RTX Spark N1X (017VN)', 'Cấu hình tham khảo: 16 inch, RAM 64 GB, SSD 2 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042104164-thumb530-20261116-1df06bb1.jpg', 139990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(134, 8, 0, 'MSI Prestige 14 Flip AI+ Vincent van Gogh Edition 14T235-259VN Ultra X9 378H', 'TGDD-MSI-EB3AC9DCCC1D', 'MSI', 'MSI Prestige 14 Flip AI+ Vincent van Gogh Edition 14T235-259VN Ultra X9 378H', 'Cấu hình tham khảo: 14 inch, Intel Core Ultra X9 378H, 32 GB RAM, 1 TB SSD, WUXGA, Windows 11.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004162-laptop-tron-20261105-600x600.png', 59990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(135, 8, 0, 'MSI Prestige 14 Flip AI+ Vincent van Gogh Edition 14T234-260VN Ultra X9 378H', 'TGDD-MSI-A1FC5430D6AF', 'MSI', 'MSI Prestige 14 Flip AI+ Vincent van Gogh Edition 14T234-260VN Ultra X9 378H', 'Cấu hình tham khảo: 14 inch, Intel Core Ultra X9 378H, 32 GB RAM, 1 TB SSD, Full HD+, Windows 11.', 'https://cdnv2.tgdd.vn/pim/cdn/images/202610/thumb124604.jpg', 59990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(136, 8, 0, 'MSI Modern 16S G3MG-016VN Core 7 350', 'TGDD-MSI-ACE707777CF2', 'MSI', 'MSI Modern 16S G3MG-016VN Core 7 350', 'Cấu hình tham khảo: 16 inch, RAM 16 GB, SSD 1 TB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004160-laptop-tron-20261104-600x600.png', 29990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(137, 8, 0, 'MSI Cyborg 15 Max C13WF i7 13620H (421VN)', 'TGDD-MSI-9F232A79F721', 'MSI', 'MSI Cyborg 15 Max C13WF i7 13620H (421VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004069-thumb530-20261116-eee45dc3.jpg', 43990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(138, 8, 0, 'MSI Cyborg 15 Max C13WE i7 13620H (422VN)', 'TGDD-MSI-131039F9744A', 'MSI', 'MSI Cyborg 15 Max C13WE i7 13620H (422VN)', 'Cấu hình tham khảo: 15.6 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004068-thumb530-20261116-eee45dc3.jpg', 41990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(139, 9, 0, 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CMHI2VN893SH)', 'TGDD-GIGABYTE-9003A413E2DA', 'Gigabyte', 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CMHI2VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003217-thumb530-20261116-d6e03b9a.jpg', 33990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(140, 9, 0, 'GIGABYTE Gaming A16 GA6H i5 13420H (GAMING-A16-CMHH2VN893SH)', 'TGDD-GIGABYTE-BCB9F4877570', 'Gigabyte', 'GIGABYTE Gaming A16 GA6H i5 13420H (GAMING-A16-CMHH2VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003192-thumb530-20261116-5785e37e.jpg', 31990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(141, 9, 0, 'GIGABYTE Gaming A16 GA6H i5 13420H (GAMING-A16-CTHH3VN893SH)', 'TGDD-GIGABYTE-3A7761C808CA', 'Gigabyte', 'GIGABYTE Gaming A16 GA6H i5 13420H (GAMING-A16-CTHH3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003357-thumb530-20261116-2d5210c2.jpg', 33990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(142, 9, 0, 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CTHI3VN893SH)', 'TGDD-GIGABYTE-2CD49896EC92', 'Gigabyte', 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CTHI3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003539-thumb530-20261116-34029b40.jpg', 35990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(143, 9, 0, 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CVHI3VN893SH)', 'TGDD-GIGABYTE-9FF1B45F69B7', 'Gigabyte', 'GIGABYTE Gaming A16 GA6H i7 13620H (GAMING-A16-CVHI3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042003358-thumb530-20261116-2d5210c2.jpg', 36490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(144, 9, 0, 'GIGABYTE GAMING A16 Ultra 9 185H (A16-EVHS3VN893SH)', 'TGDD-GIGABYTE-5AF2B551DF3F', 'Gigabyte', 'GIGABYTE GAMING A16 Ultra 9 185H (A16-EVHS3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB.', 'https://img.tgdd.vn/imgt/ecom/f_webp,fit_outside,quality_100,s_448x448/https://cdn.tgdd.vn/2026/11/timerseo/0220042004103-thumb530-20261116-d52eb866.jpg', 40490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(145, 9, 0, 'GIGABYTE GAMING EAGLE R5 7533HS (EAGLE-9MJR2VNF93SH)', 'TGDD-GIGABYTE-6F09B1EDA8A6', 'Gigabyte', 'GIGABYTE GAMING EAGLE R5 7533HS (EAGLE-9MJR2VNF93SH)', 'Cấu hình tham khảo: 16 inch, RTX 4050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004102-thumb530-20261116-6934ca4e.jpg', 31490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(146, 9, 0, 'GIGABYTE GAMING EAGLE R5 7533HS (AGLE-9LJR2VNF93SH)', 'TGDD-GIGABYTE-D0C94C31A87D', 'Gigabyte', 'GIGABYTE GAMING EAGLE R5 7533HS (AGLE-9LJR2VNF93SH)', 'Cấu hình tham khảo: 16 inch, RTX 3050 6GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004101-thumb530-20261116-eee2bcf1.jpg', 29490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(147, 9, 0, 'GIGABYTE Gaming A16 R7 260 (A16-3THK3VN893SH)', 'TGDD-GIGABYTE-E283DA653E7D', 'Gigabyte', 'GIGABYTE Gaming A16 R7 260 (A16-3THK3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5050 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003963-thumb530-20261116-114737c5.jpg', 36490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(148, 9, 0, 'GIGABYTE AERO X16 R9 AI 465 (AERO-X16-4WHN3VNC93AH)', 'TGDD-GIGABYTE-6B6262A0B050', 'Gigabyte', 'GIGABYTE AERO X16 R9 AI 465 (AERO-X16-4WHN3VNC93AH)', 'Cấu hình tham khảo: 16 inch, RTX 5070 8GB, RAM 16 GB, SSD 512 GB.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042004104-thumb530-20261116-d607747d.jpg', 54990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(149, 9, 0, 'GIGABYTE Gaming A16 R7 260 (A16-3VHK3VN893SH)', 'TGDD-GIGABYTE-50822773CA9C', 'Gigabyte', 'GIGABYTE Gaming A16 R7 260 (A16-3VHK3VN893SH)', 'Cấu hình tham khảo: 16 inch, RTX 5060 8GB, RAM 16 GB, SSD 512 GB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003964-thumb530-20261116-114737c5.jpg', 38490000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51'),
(150, 9, 0, 'GIGABYTE Gaming AERO X16 R7 AI 350 (AERO-X16-1VH93VNC94AH)', 'TGDD-GIGABYTE-34F5B325E192', 'Gigabyte', 'GIGABYTE Gaming AERO X16 R7 AI 350 (AERO-X16-1VH93VNC94AH)', 'Cấu hình tham khảo: 16 inch, RTX 5060 8GB, RAM 16 GB, SSD 1 TB, RTX AI PC.', 'https://cdn.tgdd.vn/2026/11/timerseo/0220042003965-thumb530-20261116-a99ad388.jpg', 42990000.00, 1, 0, 'approved', '2026-11-26 18:10:44', '2026-11-26 18:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `ecom_referral_clicks`
--

CREATE TABLE `ecom_referral_clicks` (
  `affiliate_user_id` int(11) NOT NULL,
  `click_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_settings`
--

CREATE TABLE `ecom_settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ecom_settings`
--

INSERT INTO `ecom_settings` (`setting_key`, `setting_value`) VALUES
('referral_commission_percent', '5'),
('seller_fee_percent', '5'),
('shipping_fee', '0');

-- --------------------------------------------------------

--
-- Table structure for table `momo`
--

CREATE TABLE `momo` (
  `id` int(11) NOT NULL,
  `request_id` varchar(64) DEFAULT NULL,
  `tranId` text DEFAULT NULL,
  `partnerId` text DEFAULT NULL,
  `partnerName` text DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `status` varchar(32) DEFAULT 'xuly',
  `deletedate` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `name`, `value`) VALUES
(1, 'tenweb', ''),
(2, 'mota', ''),
(3, 'tukhoa', ''),
(4, 'logo', 'https://i.imgur.com/iu1hc4d.gif'),
(5, 'email', ''),
(6, 'pass_email', ''),
(11, 'noidung_naptien', 'naptien '),
(12, 'thongbao', '<p><br></p><div style=\"text-align: center;\"></div><p></p>'),
(13, 'anhbia', 'https://i.imgur.com/iu1hc4d.gif'),
(14, 'favicon', 'https://i.imgur.com/iu1hc4d.gif'),
(17, 'baotri', 'ON'),
(18, 'chinhsach', '<p>BẰNG VIỆC SỬ DỤNG C&Aacute;C DỊCH VỤ HOẶC MỞ MỘT T&Agrave;I KHOẢN, BẠN CHO BIẾT RẰNG BẠN CHẤP NHẬN, KH&Ocirc;NG R&Uacute;T LẠI, C&Aacute;C ĐIỀU KHOẢN DỊCH VỤ N&Agrave;Y. NẾU BẠN KH&Ocirc;NG ĐỒNG &Yacute; VỚI C&Aacute;C ĐIỀU KHOẢN N&Agrave;Y, VUI L&Ograve;NG KH&Ocirc;NG SỬ DỤNG C&Aacute;C DỊCH VỤ CỦA CH&Uacute;NG T&Ocirc;I HAY TRUY CẬP TRANG WEB. NẾU BẠN DƯỚI 18 TUỔI HOẶC &quot;ĐỘ TUỔI TRƯỞNG TH&Agrave;NH&quot;PH&Ugrave; HỢP Ở NƠI BẠN SỐNG, BẠN PHẢI XIN PH&Eacute;P CHA MẸ HOẶC NGƯỜI GI&Aacute;M HỘ HỢP PH&Aacute;P ĐỂ MỞ MỘT T&Agrave;I KHOẢN V&Agrave; CHA MẸ HOẶC NGƯỜI GI&Aacute;M HỘ HỢP PH&Aacute;P PHẢI ĐỒNG &Yacute; VỚI C&Aacute;C ĐIỀU KHOẢN DỊCH VỤ N&Agrave;Y. NẾU BẠN KH&Ocirc;NG BIẾT BẠN C&Oacute; THUỘC &quot;ĐỘ TUỔI TRƯỞNG TH&Agrave;NH&quot; Ở NƠI BẠN SỐNG HAY KH&Ocirc;NG, HOẶC KH&Ocirc;NG HIỂU PHẦN N&Agrave;Y, VUI L&Ograve;NG KH&Ocirc;NG TẠO T&Agrave;I KHOẢN CHO ĐẾN KHI BẠN Đ&Atilde; NHỜ CHA MẸ HOẶC NGƯỜI GI&Aacute;M HỘ HỢP PH&Aacute;P CỦA BẠN GI&Uacute;P ĐỠ. NẾU BẠN L&Agrave; CHA MẸ HOẶC NGƯỜI GI&Aacute;M HỘ HỢP PH&Aacute;P CỦA MỘT TRẺ VỊ TH&Agrave;NH NI&Ecirc;N MUỐN TẠO MỘT T&Agrave;I KHOẢN, BẠN PHẢI CHẤP NHẬN C&Aacute;C ĐIỀU KHOẢN DỊCH VỤ N&Agrave;Y THAY MẶT CHO TRẺ VỊ TH&Agrave;NH NI&Ecirc;N Đ&Oacute; V&Agrave; BẠN SẼ CHỊU TR&Aacute;CH NHIỆM ĐỐI VỚI TẤT CẢ HOẠT ĐỘNG SỬ DỤNG T&Agrave;I KHOẢN HAY C&Aacute;C DỊCH VỤ, BAO GỒM C&Aacute;C GIAO DỊCH MUA H&Agrave;NG DO TRẺ VỊ TH&Agrave;NH NI&Ecirc;N THỰC HIỆN, CHO D&Ugrave; T&Agrave;I KHOẢN CỦA TRẺ VỊ TH&Agrave;NH NI&Ecirc;N Đ&Oacute; ĐƯỢC MỞ V&Agrave;O L&Uacute;C N&Agrave;Y HAY ĐƯỢC TẠO SAU N&Agrave;Y V&Agrave; CHO D&Ugrave; TRẺ VỊ TH&Agrave;NH NI&Ecirc;N C&Oacute; ĐƯỢC BẠN GI&Aacute;M S&Aacute;T TRONG GIAO DỊCH MUA H&Agrave;NG Đ&Oacute; HAY KH&Ocirc;NG.</p>\r\n'),
(27, 'min_ruttien', '100000'),
(28, 'ck_con', '3'),
(29, 'phi_chuyentien', '500'),
(30, 'status_chuyentien', 'ON'),
(31, 'hotline', ''),
(32, 'facebook', 'https://www.facebook.com/namvo.az'),
(33, 'theme_color', '#01578B'),
(34, 'modal_thongbao', ''),
(42, 'api_bank', '8cea532af8b63ef4355880fe74f8c52c'),
(43, 'status_napbank', 'ON'),
(44, 'status_demo', 'OFF'),
(45, 'api_card', 'CDEGexRWklBVPfmjQTXMFSZHgqzNhJcdwpKIYobnratvLyAsiUOu'),
(46, 'luuy_naptien', '<ul style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; outline: 0px; border: 0px; list-style-position: inside; color: rgb(51, 51, 51); font-family: roboto, Arial, Tahoma, sans-serif, arial, Helvetica; font-size: 14px;\"><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Hệ thống xử lý 5s 1 thẻ.</li><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Vui lòng gửi đúng mệnh giá, sai mệnh giá thực nhận mệnh giá bé nhất.</li><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Ví dụ mệnh giá thực là 100k, quý khách nạp 100k thực nhận 100k.</li></ul><p style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Ví dụ mệnh giá thực là 100k quý khách nạp 50k thực nhận 45k.</p>'),
(47, 'id_video_youtube', ''),
(48, 'ck_bank', '0'),
(49, 'luuy_napbank', '<p><span style=\"font-weight: bolder; caret-color: rgb(230, 77, 77); color: rgb(230, 77, 77); font-size: 1rem;\">NẠP QUA Ví MoMo VÀ MBbank ĐƯỢC CỘNG THÊM 20%</span></p><p><font color=\"#e64d4d\"><span style=\"caret-color: rgb(230, 77, 77);\"><span style=\"font-weight: bolder;\">VÍ DỤ BẠN NẠP 100K QUA BANK THÌ SẼ CÓ 120K TRONG WEB</span></span></font></p><p><font color=\"#e64d4d\"><span style=\"caret-color: rgb(230, 77, 77);\"><span style=\"font-weight: bolder;\">200K QUA BANK THÌ CÓ 240K TRONG WEB</span></span></font></p><p><br></p>'),
(50, 'check_time_cron', '1670307002'),
(51, 'api_momo', ''),
(52, 'stk_bank', '11996371'),
(53, 'mk_bank', '29082005Nam@'),
(54, 'check_time_cron_bank', '1794825157'),
(55, 'html_footer', ''),
(56, 'text_left_footer', ''),
(57, 'text_center_footer', ''),
(58, 'email_admin', ''),
(59, 'button_buy', '1'),
(60, 'block_f12', 'ON'),
(61, 'license_key', '4a866766abc63ffdf5b30e6d4ce07a'),
(62, 'btnSaveLicense', ''),
(63, 'ck_card', '0'),
(64, 'logo_dark', 'assets/storage/theme/logo_dark4D9.png'),
(65, 'background', 'assets/storage/theme/backgroundZM2.png'),
(66, 'Partner_ID', ''),
(67, 'Partner_Key', ''),
(68, 'youtube', ''),
(69, 'discord', ''),
(70, 'telegram', 'https://t.me/tannam76'),
(71, 'zalo', 'https://zalo.me/tannam76'),
(72, 'home_banners', 'https://i.ibb.co/jPXBPjT2/2a0790ef915e.png\r\nhttps://i.ibb.co/nWz1hKF/965887440647.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `money` int(11) NOT NULL DEFAULT 0,
  `item` bigint(20) DEFAULT 0,
  `level` varchar(255) DEFAULT NULL,
  `banned` int(11) NOT NULL DEFAULT 0,
  `createdate` datetime DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `daily` int(11) DEFAULT 0,
  `otp` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `chietkhau` float DEFAULT 0,
  `time` varchar(255) DEFAULT NULL,
  `chitieu` int(11) NOT NULL DEFAULT 0,
  `total_money` int(11) NOT NULL DEFAULT 0,
  `ctv` int(11) DEFAULT 0,
  `ctvacc` int(11) DEFAULT 0,
  `google_sub` varchar(255) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `otp_created_at` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `token`, `money`, `item`, `level`, `banned`, `createdate`, `email`, `ref`, `daily`, `otp`, `ip`, `chietkhau`, `time`, `chitieu`, `total_money`, `ctv`, `ctvacc`, `google_sub`, `otp_created_at`) VALUES
(8, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'QRXbxEUnMjAlZvzqTDNampIyidCOkfYoFrgPWsGhVBwJLuHceKSt', 335119, 83833, 'admin', 0, '2023-08-14 12:32:33', '', NULL, 0, '', '14.254.126.39', 0, '1692016353', 0, 10000, 0, 0, NULL, NULL),
(9, 'nam123', '00a79c670f4b694a87b57795144bf546', 'rPuWNeLRIgtodxBUnGkAamTFVbhKMsCZvwSHOYEfJDyjqizclXQp', 0, 0, '', 0, '2026-11-16 07:50:43', '', '', 0, '', '::1', 0, '1794811843', 0, 0, 1, 0, NULL, NULL),
(10, 'nam1234', 'a160e83d348a1d0e4a009e3e44908ebb', 'viclTHJUQEtuzMkDOjyLwPagrbVXfNdSmIFZxRqKBnWeGshYCpoA', 0, 0, '', 0, '2026-11-26 12:48:27', '', '', 0, '', '::1', 0, '1795693707', 0, 0, 1, 0, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `bank_auto`
--
ALTER TABLE `bank_auto`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `dongtien`
--
ALTER TABLE `dongtien`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `ecom_bank_payments`
--
ALTER TABLE `ecom_bank_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_bank_transaction` (`transaction_id`),
  ADD KEY `idx_ecom_bank_order` (`order_id`);

--
-- Indexes for table `ecom_categories`
--
ALTER TABLE `ecom_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_categories_slug` (`slug`),
  ADD KEY `idx_ecom_categories_status` (`status`,`sort_order`);

--
-- Indexes for table `ecom_commissions`
--
ALTER TABLE `ecom_commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_commission_key` (`commission_key`),
  ADD KEY `idx_ecom_commission_ctv` (`ctv_user_id`,`status`,`created_at`),
  ADD KEY `idx_ecom_commission_order` (`order_id`);

--
-- Indexes for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_orders_order_no` (`order_no`),
  ADD KEY `idx_ecom_orders_customer` (`customer_user_id`,`created_at`),
  ADD KEY `idx_ecom_orders_status` (`status`,`created_at`),
  ADD KEY `idx_ecom_orders_referrer` (`referrer_user_id`,`created_at`);

--
-- Indexes for table `ecom_order_items`
--
ALTER TABLE `ecom_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ecom_order_items_order` (`order_id`),
  ADD KEY `idx_ecom_order_items_seller` (`seller_user_id`,`order_id`);

--
-- Indexes for table `ecom_payouts`
--
ALTER TABLE `ecom_payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ecom_payout_ctv` (`ctv_user_id`,`status`,`created_at`);

--
-- Indexes for table `ecom_payout_items`
--
ALTER TABLE `ecom_payout_items`
  ADD PRIMARY KEY (`payout_id`,`commission_id`),
  ADD UNIQUE KEY `uq_ecom_payout_commission` (`commission_id`);

--
-- Indexes for table `ecom_products`
--
ALTER TABLE `ecom_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_products_sku` (`sku`),
  ADD KEY `idx_ecom_products_catalog` (`approval_status`,`category_id`,`created_at`),
  ADD KEY `idx_ecom_products_seller` (`seller_user_id`,`created_at`);

--
-- Indexes for table `ecom_referral_clicks`
--
ALTER TABLE `ecom_referral_clicks`
  ADD PRIMARY KEY (`affiliate_user_id`);

--
-- Indexes for table `ecom_settings`
--
ALTER TABLE `ecom_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `momo`
--
ALTER TABLE `momo`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `uq_users_google_sub` (`google_sub`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bank_auto`
--
ALTER TABLE `bank_auto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dongtien`
--
ALTER TABLE `dongtien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `ecom_bank_payments`
--
ALTER TABLE `ecom_bank_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ecom_categories`
--
ALTER TABLE `ecom_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ecom_commissions`
--
ALTER TABLE `ecom_commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ecom_order_items`
--
ALTER TABLE `ecom_order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ecom_payouts`
--
ALTER TABLE `ecom_payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecom_products`
--
ALTER TABLE `ecom_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;

--
-- AUTO_INCREMENT for table `momo`
--
ALTER TABLE `momo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
