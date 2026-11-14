-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2026 at 06:43 AM
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

--
-- Dumping data for table `dongtien`
--

INSERT INTO `dongtien` (`id`, `sotientruoc`, `sotienthaydoi`, `sotiensau`, `thoigian`, `noidung`, `username`) VALUES
(161, 0, 1000000, 1000000, '2023-09-07 07:09:58', 'Admin cộng tiền (gg)', 'admin'),
(162, 1029000, 29000, 1000000, '2023-09-07 07:10:26', 'Thực hiện (VÒNG QUAY MP40 MÃNG XÀ)', 'admin'),
(163, 1000000, 29000, 971000, '2023-09-07 07:10:35', 'Thực hiện (VÒNG QUAY MP40 MÃNG XÀ)', 'admin'),
(164, 962000, 20000, 942000, '2023-09-07 07:42:12', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(165, 942000, 20000, 922000, '2023-09-07 07:42:19', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(166, 922000, 20000, 902000, '2023-09-07 07:42:19', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(167, 882000, 111111, 993111, '2023-09-07 07:43:33', 'Admin cộng tiền ()', 'admin'),
(168, 1003110, 9999, 993111, '2023-11-26 09:30:32', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(169, 993111, 9999, 983112, '2023-11-26 09:30:39', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(170, 1002113, 29000, 973113, '2023-11-26 09:30:54', 'Thực hiện (VÒNG QUAY MP40 MÃNG XÀ)', 'admin'),
(171, 973113, 29000, 944113, '2023-11-26 09:31:03', 'Thực hiện (VÒNG QUAY MP40 MÃNG XÀ)', 'admin'),
(172, 925112, 9999, 915113, '2025-02-11 15:09:07', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(173, 915113, 9999, 905114, '2025-02-11 21:27:43', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(174, 905114, 9999, 895115, '2025-02-11 21:27:51', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(175, 895115, 9999, 885116, '2025-02-11 21:27:52', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(176, 895117, 20000, 875117, '2025-02-11 21:28:06', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(177, 875117, 20000, 855117, '2025-02-11 21:28:06', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(178, 855117, 20000, 835117, '2025-02-11 21:28:06', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(179, 825116, 9999, 815117, '2025-02-11 21:48:36', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin'),
(180, 825118, 20000, 805118, '2025-02-11 21:48:47', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(181, 805118, 20000, 785118, '2025-02-11 21:52:07', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(182, 785118, 20000, 765118, '2025-02-11 22:17:13', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(183, 765118, 20000, 745118, '2025-02-11 22:18:09', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(184, 745118, 20000, 725118, '2025-02-11 22:18:22', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(185, 725118, 20000, 705118, '2025-02-12 21:55:22', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(186, 705118, 20000, 685118, '2025-02-12 21:56:40', 'Thực hiện (Vòng Quay M4A1 Hoả Ngục)', 'admin'),
(187, 685118, 20000, 665118, '2025-02-14 22:03:58', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(188, 665118, 20000, 645118, '2025-02-14 22:03:58', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(189, 645118, 20000, 625118, '2025-02-14 22:06:26', 'Thực hiện (VÒNG QUAY BÃI BIỂN)', 'admin'),
(190, 635118, 30000, 605118, '2025-02-17 11:36:07', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(191, 605118, 30000, 575118, '2025-02-17 11:36:18', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(192, 575118, 30000, 545118, '2025-02-17 11:36:18', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(193, 545118, 30000, 515118, '2025-02-17 11:36:30', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(194, 515118, 30000, 485118, '2025-02-17 11:36:30', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(195, 485118, 30000, 455118, '2025-02-17 16:15:07', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(196, 455118, 30000, 425118, '2025-02-17 16:18:53', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(197, 425118, 30000, 395118, '2025-02-17 16:19:00', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(198, 395118, 30000, 365118, '2025-02-17 16:19:11', 'Thực hiện (Vòng Quay Kim Cương)', 'admin'),
(199, 345117, 9999, 335118, '2025-02-17 16:32:07', 'Thực hiện (VÒNG QUAY THIÊN THẦN BẠCH KIM)', 'admin');

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
(1, 'TV', 'cat-4d004673e6', '', 0, 'active', '2026-11-14 04:56:38');

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
(1, 'ECAA71BE5B8E2E6C104A5BF966', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'trt', 'Hà Nội', '', NULL, NULL, NULL, 10000.00, 0.00, 10000.00, 'cod', 'processing', 'unpaid', '2026-11-14 05:03:45', NULL, '2026-11-14 05:03:45'),
(2, 'ECB77DFD6DC25AFA3F0C3541CE', 8, 0, 'admin', 'admin123@gmail.com', '+84324667788', 'thht', 'Cần Thơ', 'rett', NULL, NULL, NULL, 5000.00, 0.00, 5000.00, 'bank_transfer', 'pending_payment', 'unpaid', '2026-11-14 05:04:10', NULL, '2026-11-14 05:04:10');

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
(2, 2, 1, 0, 'fsf', 'FGDGF', 5000.00, 1, 5000.00);

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
(1, 1, 0, 'fsf', 'FGDGF', 'hghgh', 'ghh', 'hghgh', 'https://cdn.upanhlaylink.com/i/s5GZH6Iz.png', 5000.00, 996, 12, 'approved', '2026-11-14 04:59:14', '2026-11-14 05:04:10');

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
(42, 'api_bank', ''),
(43, 'status_napbank', 'OFF'),
(44, 'status_demo', 'OFF'),
(45, 'api_card', 'CDEGexRWklBVPfmjQTXMFSZHgqzNhJcdwpKIYobnratvLyAsiUOu'),
(46, 'luuy_naptien', '<ul style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; outline: 0px; border: 0px; list-style-position: inside; color: rgb(51, 51, 51); font-family: roboto, Arial, Tahoma, sans-serif, arial, Helvetica; font-size: 14px;\"><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Hệ thống xử lý 5s 1 thẻ.</li><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Vui lòng gửi đúng mệnh giá, sai mệnh giá thực nhận mệnh giá bé nhất.</li><li style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Ví dụ mệnh giá thực là 100k, quý khách nạp 100k thực nhận 100k.</li></ul><p style=\"padding: 0px; margin: 0px; outline: 0px; border: 0px;\">Ví dụ mệnh giá thực là 100k quý khách nạp 50k thực nhận 45k.</p>'),
(47, 'id_video_youtube', ''),
(48, 'ck_bank', '0'),
(49, 'luuy_napbank', '<p><span style=\"font-weight: bolder; caret-color: rgb(230, 77, 77); color: rgb(230, 77, 77); font-size: 1rem;\">NẠP QUA Ví MoMo VÀ MBbank ĐƯỢC CỘNG THÊM 20%</span></p><p><font color=\"#e64d4d\"><span style=\"caret-color: rgb(230, 77, 77);\"><span style=\"font-weight: bolder;\">VÍ DỤ BẠN NẠP 100K QUA BANK THÌ SẼ CÓ 120K TRONG WEB</span></span></font></p><p><font color=\"#e64d4d\"><span style=\"caret-color: rgb(230, 77, 77);\"><span style=\"font-weight: bolder;\">200K QUA BANK THÌ CÓ 240K TRONG WEB</span></span></font></p><p><br></p>'),
(50, 'check_time_cron', '1670307002'),
(51, 'api_momo', ''),
(52, 'stk_bank', ''),
(53, 'mk_bank', ''),
(54, 'check_time_cron_bank', '1650418673'),
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
  `ctvacc` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `token`, `money`, `item`, `level`, `banned`, `createdate`, `email`, `ref`, `daily`, `otp`, `ip`, `chietkhau`, `time`, `chitieu`, `total_money`, `ctv`, `ctvacc`) VALUES
(8, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'QRXbxEUnMjAlZvzqTDNampIyidCOkfYoFrgPWsGhVBwJLuHceKSt', 325119, 83833, 'admin', 0, '2023-08-14 12:32:33', '', NULL, 0, '', '14.254.126.39', 0, '1692016353', 0, 0, 0, 0);

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
  ADD PRIMARY KEY (`id`) USING BTREE;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dongtien`
--
ALTER TABLE `dongtien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `ecom_bank_payments`
--
ALTER TABLE `ecom_bank_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecom_categories`
--
ALTER TABLE `ecom_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ecom_commissions`
--
ALTER TABLE `ecom_commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ecom_order_items`
--
ALTER TABLE `ecom_order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ecom_payouts`
--
ALTER TABLE `ecom_payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecom_products`
--
ALTER TABLE `ecom_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
