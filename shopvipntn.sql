SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;

SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;

SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;

SET NAMES utf8mb4;

CREATE TABLE `bank` (
  `id` int(11) NOT NULL,
  `stk` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `bank_name` text DEFAULT NULL,
  `chi_nhanh` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `ghichu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

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

CREATE TABLE `dongtien` (
  `id` int(11) NOT NULL,
  `sotientruoc` int(11) DEFAULT NULL,
  `sotienthaydoi` int(11) DEFAULT NULL,
  `sotiensau` int(11) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `noidung` text DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

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

CREATE TABLE `ecom_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','hidden') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `ecom_payouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ctv_user_id` int(11) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `status` enum('requested','approved','paid','rejected') NOT NULL DEFAULT 'requested',
  `payout_reference` varchar(190) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ecom_payout_items` (
  `payout_id` int(10) UNSIGNED NOT NULL,
  `commission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `ecom_referral_clicks` (
  `affiliate_user_id` int(11) NOT NULL,
  `click_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ecom_settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` varchar(190) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

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

ALTER TABLE `bank`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `bank_auto`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `dongtien`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `ecom_bank_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_bank_transaction` (`transaction_id`),
  ADD KEY `idx_ecom_bank_order` (`order_id`);

ALTER TABLE `ecom_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_categories_slug` (`slug`),
  ADD KEY `idx_ecom_categories_status` (`status`,`sort_order`);

ALTER TABLE `ecom_commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_commission_key` (`commission_key`),
  ADD KEY `idx_ecom_commission_ctv` (`ctv_user_id`,`status`,`created_at`),
  ADD KEY `idx_ecom_commission_order` (`order_id`);

ALTER TABLE `ecom_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_orders_order_no` (`order_no`),
  ADD KEY `idx_ecom_orders_customer` (`customer_user_id`,`created_at`),
  ADD KEY `idx_ecom_orders_status` (`status`,`created_at`),
  ADD KEY `idx_ecom_orders_referrer` (`referrer_user_id`,`created_at`);

ALTER TABLE `ecom_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ecom_order_items_order` (`order_id`),
  ADD KEY `idx_ecom_order_items_seller` (`seller_user_id`,`order_id`);

ALTER TABLE `ecom_payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ecom_payout_ctv` (`ctv_user_id`,`status`,`created_at`);

ALTER TABLE `ecom_payout_items`
  ADD PRIMARY KEY (`payout_id`,`commission_id`),
  ADD UNIQUE KEY `uq_ecom_payout_commission` (`commission_id`);

ALTER TABLE `ecom_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ecom_products_sku` (`sku`),
  ADD KEY `idx_ecom_products_catalog` (`approval_status`,`category_id`,`created_at`),
  ADD KEY `idx_ecom_products_seller` (`seller_user_id`,`created_at`);

ALTER TABLE `ecom_referral_clicks`
  ADD PRIMARY KEY (`affiliate_user_id`);

ALTER TABLE `ecom_settings`
  ADD PRIMARY KEY (`setting_key`);

ALTER TABLE `momo`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `options`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `uq_users_google_sub` (`google_sub`);

ALTER TABLE `bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bank_auto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `dongtien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_bank_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_payouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecom_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `momo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;

SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;

SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;
