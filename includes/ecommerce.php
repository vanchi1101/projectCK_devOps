<?php

function ecom_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ecom_image_url_is_valid($value)
{
    $value = (string) $value;
    if ($value === '') {
        return true;
    }
    if (strlen($value) > 500) {
        return false;
    }
    if (preg_match('#\A/assets/storage/images/products/[A-Za-z0-9_-]+\.(?:avif|gif|jpe?g|png|webp)\z#i', $value) === 1) {
        return true;
    }

    if (filter_var($value, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
}

function ecom_is_installed()
{
    global $AZCODE;
    return (bool) $AZCODE->get_row("SHOW TABLES LIKE 'ecom_products'");
}

function ecom_supports_cash_on_delivery()
{
    global $AZCODE;
    return (bool) $AZCODE->get_row("SHOW COLUMNS FROM `ecom_orders` LIKE 'payment_method'");
}

function ecom_allowed_order_statuses($order)
{
    $currentStatus = (string) ($order['status'] ?? '');
    $paymentStatus = (string) ($order['payment_status'] ?? '');
    $nextStatuses = [];

    switch ($currentStatus) {
        case 'pending_payment':
            if ($paymentStatus === 'unpaid') {
                $nextStatuses[] = 'cancelled';
            }
            break;
        case 'paid':
            $nextStatuses = ['processing', 'shipped', 'completed'];
            if ($paymentStatus === 'paid') {
                $nextStatuses[] = 'refunded';
            }
            break;
        case 'processing':
            $nextStatuses = ['shipped', 'completed'];
            if ($paymentStatus === 'unpaid') {
                $nextStatuses[] = 'cancelled';
            } elseif ($paymentStatus === 'paid') {
                $nextStatuses[] = 'refunded';
            }
            break;
        case 'shipped':
            $nextStatuses[] = 'completed';
            if ($paymentStatus === 'paid') {
                $nextStatuses[] = 'refunded';
            }
            break;
        case 'completed':
            if ($paymentStatus === 'paid') {
                $nextStatuses[] = 'refunded';
            }
            break;
        case 'payment_review':
            if (in_array($paymentStatus, ['paid', 'mismatch'], true)) {
                $nextStatuses[] = 'refunded';
            }
            break;
    }

    return array_values(array_unique(array_merge([$currentStatus], $nextStatuses)));
}

function ecom_csrf_token()
{
    if (empty($_SESSION['ecom_csrf'])) {
        $_SESSION['ecom_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['ecom_csrf'];
}

function ecom_check_csrf()
{
    $submitted = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    return isset($_SESSION['ecom_csrf']) && hash_equals($_SESSION['ecom_csrf'], $submitted);
}

function ecom_setting($key, $default = '')
{
    global $AZCODE;
    $key = $AZCODE->escape($key);
    $row = $AZCODE->get_row("SELECT `setting_value` FROM `ecom_settings` WHERE `setting_key` = '$key'");
    return $row ? $row['setting_value'] : $default;
}

function ecom_commission_kind_for_user($userId)
{
    global $AZCODE;
    $userId = (int) $userId;
    if ($userId <= 0) {
        return null;
    }

    $user = $AZCODE->get_row(
        "SELECT `level`,`banned`,`ctv` FROM `users` WHERE `id` = '$userId'"
    );
    if (!$user || (int) $user['banned'] !== 0 || $user['level'] === 'admin') {
        return null;
    }
    if ((int) $user['ctv'] === 1) {
        return 'seller';
    }
    return 'referral';
}

function ecom_capture_referrer()
{
    global $AZCODE;
    if (empty($_GET['ref'])) {
        return;
    }

    $token = $AZCODE->escape(trim((string) $_GET['ref']));
    $user = $AZCODE->get_row("SELECT `id` FROM `users` WHERE `token` = '$token'");
    if ($user && ecom_commission_kind_for_user($user['id']) === 'referral') {
        $referrerId = (int) $user['id'];
        $_SESSION['ecom_referrer_id'] = $referrerId;
        global $getUser;
        if (empty($getUser['id']) || (int) $getUser['id'] !== $referrerId) {
            ecom_record_referral_click($referrerId);
        }
    }
}

function ecom_record_referral_click($referrerId)
{
    global $AZCODE;
    $referrerId = (int) $referrerId;
    if ($referrerId <= 0) {
        return false;
    }

    $now = $AZCODE->escape(date('Y-m-d H:i:s'));
    $recorded = $AZCODE->query(
        "INSERT INTO `ecom_referral_clicks` (`affiliate_user_id`,`click_count`,`updated_at`)"
        . " VALUES ('$referrerId',1,'$now')"
        . " ON DUPLICATE KEY UPDATE `click_count` = `click_count` + 1, `updated_at` = '$now'"
    );
    if (!$recorded) {
        error_log('Unable to record e-commerce referral click for user ' . $referrerId);
    }
    return (bool) $recorded;
}

function ecom_cart()
{
    if (!isset($_SESSION['ecom_cart']) || !is_array($_SESSION['ecom_cart'])) {
        $_SESSION['ecom_cart'] = [];
    }
    return $_SESSION['ecom_cart'];
}

function ecom_cart_items()
{
    global $AZCODE;
    $cart = ecom_cart();
    $ids = [];
    foreach ($cart as $id => $quantity) {
        $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id !== false && (int) $quantity > 0) {
            $ids[(int) $id] = min(99, (int) $quantity);
        }
    }
    if (!$ids) {
        return [];
    }

    $list = implode(',', array_keys($ids));
    $products = $AZCODE->get_list(
        "SELECT `id`,`name`,`sku`,`image_url`,`price`,`stock`,`approval_status`"
        . " FROM `ecom_products` WHERE `id` IN ($list) AND `approval_status` = 'approved'"
    );
    foreach ($products as &$product) {
        $product['quantity'] = $ids[(int) $product['id']];
        $product['line_total'] = (float) $product['price'] * $product['quantity'];
    }
    unset($product);
    return $products;
}

function ecom_vietnam_provinces()
{
    return [
        'An Giang',
        'Bắc Ninh',
        'Cà Mau',
        'Cao Bằng',
        'Cần Thơ',
        'Đà Nẵng',
        'Đắk Lắk',
        'Điện Biên',
        'Đồng Nai',
        'Đồng Tháp',
        'Gia Lai',
        'Hà Nội',
        'Hà Tĩnh',
        'Hải Phòng',
        'Huế',
        'Hưng Yên',
        'Khánh Hòa',
        'Lai Châu',
        'Lạng Sơn',
        'Lào Cai',
        'Lâm Đồng',
        'Nghệ An',
        'Ninh Bình',
        'Phú Thọ',
        'Quảng Ngãi',
        'Quảng Ninh',
        'Quảng Trị',
        'Sơn La',
        'Tây Ninh',
        'Thái Nguyên',
        'Thanh Hóa',
        'Thành phố Hồ Chí Minh',
        'Tuyên Quang',
        'Vĩnh Long'
    ];
}

function ecom_create_order($customer, $input)
{
    global $AZCODE;

    $cart = ecom_cart();
    $ids = [];
    foreach ($cart as $id => $quantity) {
        $validId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
        if ($validId !== false && $quantity !== false) {
            $ids[(int) $validId] = (int) $quantity;
        }
    }
    if (!$ids) {
        return ['error' => 'Giỏ hàng đang trống.'];
    }

    $name = trim((string) ($input['customer_name'] ?? ''));
    $phone = trim((string) ($input['customer_phone'] ?? ''));
    $address = trim((string) ($input['shipping_address'] ?? ''));
    $city = trim((string) ($input['shipping_city'] ?? ''));
    $email = trim((string) ($input['customer_email'] ?? ''));
    if ($email === '') {
        $email = trim((string) ($customer['email'] ?? ''));
    }
    $note = trim((string) ($input['shipping_note'] ?? ''));
    $paymentMethod = (string) ($input['payment_method'] ?? 'bank_transfer');
    if ($name === '' || $phone === '' || $address === '' || $city === '') {
        return ['error' => 'Vui lòng nhập đầy đủ tên, số điện thoại và địa chỉ nhận hàng.'];
    }
    if (!in_array($paymentMethod, ['bank_transfer', 'cod'], true)) {
        return ['error' => 'Phương thức thanh toán không hợp lệ.'];
    }
    if ($paymentMethod === 'cod' && !ecom_supports_cash_on_delivery()) {
        return ['error' => 'Cửa hàng chưa cập nhật cơ sở dữ liệu để hỗ trợ thanh toán khi nhận hàng.'];
    }
    if (!in_array($city, ecom_vietnam_provinces(), true)) {
        return ['error' => 'Vui lòng chọn tỉnh hoặc thành phố trong danh sách.'];
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Vui lòng nhập địa chỉ email hợp lệ để nhận thông tin đơn hàng.'];
    }
    if (strlen($name) > 190 || strlen($phone) > 40 || strlen($address) > 500 || strlen($city) > 120 || strlen($email) > 190) {
        return ['error' => 'Thông tin giao hàng vượt quá độ dài cho phép.'];
    }

    if (!$AZCODE->begin_transaction()) {
        return ['error' => 'Không thể khởi tạo giao dịch đặt hàng.'];
    }

    try {
        $items = [];
        $subtotal = 0.0;
        foreach ($ids as $id => $quantity) {
            $product = $AZCODE->get_row(
                "SELECT `id`,`seller_user_id`,`name`,`sku`,`price`,`stock`,`approval_status`"
                . " FROM `ecom_products` WHERE `id` = '$id' FOR UPDATE"
            );
            if (!$product || $product['approval_status'] !== 'approved') {
                throw new RuntimeException('Một sản phẩm trong giỏ không còn bán.');
            }
            if ((int) $product['stock'] < $quantity) {
                throw new RuntimeException('Sản phẩm "' . $product['name'] . '" không đủ tồn kho.');
            }
            $product['quantity'] = $quantity;
            $product['line_total'] = round((float) $product['price'] * $quantity, 2);
            $subtotal += $product['line_total'];
            $items[] = $product;
        }

        $shippingFee = max(0, (float) ecom_setting('shipping_fee', '0'));
        $total = round($subtotal + $shippingFee, 2);
        $orderNo = 'EC' . strtoupper(bin2hex(random_bytes(12)));
        $referrerId = isset($_SESSION['ecom_referrer_id']) ? (int) $_SESSION['ecom_referrer_id'] : null;
        if ($referrerId === (int) $customer['id'] || ecom_commission_kind_for_user($referrerId) !== 'referral') {
            $referrerId = null;
        }

        $createdAt = date('Y-m-d H:i:s');
        $orderData = [
            'order_no' => $orderNo,
            'customer_user_id' => (int) $customer['id'],
            'referrer_user_id' => $referrerId ?: 0,
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'shipping_address' => $address,
            'shipping_city' => $city,
            'shipping_note' => $note,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'shipping_fee' => number_format($shippingFee, 2, '.', ''),
            'total' => number_format($total, 2, '.', ''),
            'status' => $paymentMethod === 'cod' ? 'processing' : 'pending_payment',
            'payment_status' => 'unpaid',
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
        if (ecom_supports_cash_on_delivery()) {
            $orderData['payment_method'] = $paymentMethod;
        }
        $created = $AZCODE->insert('ecom_orders', $orderData);
        if (!$created) {
            throw new RuntimeException('Không thể tạo đơn hàng.');
        }
        $orderId = (int) $AZCODE->insert_id();

        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $productId = (int) $item['id'];
            $lineTotal = number_format($item['line_total'], 2, '.', '');
            $inserted = $AZCODE->insert('ecom_order_items', [
                'order_id' => $orderId,
                'product_id' => $productId,
                'seller_user_id' => $item['seller_user_id'] !== null ? (int) $item['seller_user_id'] : 0,
                'product_name' => $item['name'],
                'sku' => $item['sku'],
                'unit_price' => number_format((float) $item['price'], 2, '.', ''),
                'quantity' => $quantity,
                'line_total' => $lineTotal
            ]);
            if (!$inserted) {
                throw new RuntimeException('Không thể lưu chi tiết đơn hàng.');
            }
            if (!$AZCODE->query(
                "UPDATE `ecom_products` SET `stock` = `stock` - '$quantity', `updated_at` = '$createdAt'"
                . " WHERE `id` = '$productId' AND `stock` >= '$quantity'"
            )) {
                throw new RuntimeException('Không thể cập nhật tồn kho.');
            }
        }

        if (!$AZCODE->commit()) {
            throw new RuntimeException('Không thể xác nhận đơn hàng.');
        }
        unset($_SESSION['ecom_cart']);
        return ['order_no' => $orderNo];
    } catch (Throwable $error) {
        $AZCODE->rollback();
        return ['error' => $error->getMessage()];
    }
}

function ecom_parse_order_no($description)
{
    if (preg_match('/\b(EC[A-F0-9]{24})\b/i', (string) $description, $matches)) {
        return strtoupper($matches[1]);
    }
    return null;
}

function ecom_process_bank_payment($transactionId, $description, $amount)
{
    global $AZCODE;
    $orderNo = ecom_parse_order_no($description);
    if ($orderNo === null) {
        return 'not_ecommerce';
    }
    if (!ecom_is_installed()) {
        return 'not_ready';
    }

    $tid = trim((string) $transactionId);
    if (!preg_match('/^[a-zA-Z0-9_-]{1,128}$/D', $tid)) {
        return 'invalid_transaction';
    }
    $orderNoSql = $AZCODE->escape($orderNo);

    if (!$AZCODE->begin_transaction()) {
        return 'error';
    }
    try {
        $tidSql = $AZCODE->escape($tid);
        $existingPayment = $AZCODE->get_row(
            "SELECT `id` FROM `ecom_bank_payments` WHERE `transaction_id` = '$tidSql' LIMIT 1"
        );
        if ($existingPayment) {
            $AZCODE->rollback();
            return 'duplicate';
        }

        $order = $AZCODE->get_row(
            "SELECT * FROM `ecom_orders` WHERE `order_no` = '$orderNoSql' FOR UPDATE"
        );
        if (!$order) {
            $AZCODE->rollback();
            return 'unknown_order';
        }

        $amount = round((float) $amount, 2);
        $isExact = abs($amount - (float) $order['total']) < 0.01;
        $canPay = (!isset($order['payment_method']) || $order['payment_method'] === 'bank_transfer')
            && in_array($order['status'], ['pending_payment', 'payment_review'], true)
            && $order['payment_status'] !== 'paid';
        $status = $isExact && $canPay ? 'accepted' : 'mismatch';
        $inserted = $AZCODE->insert('ecom_bank_payments', [
            'provider' => 'bank',
            'transaction_id' => $tid,
            'order_id' => (int) $order['id'],
            'amount' => number_format($amount, 2, '.', ''),
            'description' => (string) $description,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        if (!$inserted) {
            $AZCODE->rollback();
            return 'duplicate';
        }

        if (!$isExact || !$canPay) {
            if (!$canPay) {
                if (!$AZCODE->query(
                    "UPDATE `ecom_bank_payments` SET `status` = 'duplicate_review' WHERE `transaction_id` = '$tid'"
                )) {
                    throw new RuntimeException('Unable to flag duplicate bank payment for review');
                }
            } else {
                if (!$AZCODE->query(
                    "UPDATE `ecom_orders` SET `status` = 'payment_review', `payment_status` = 'mismatch',"
                    . " `updated_at` = '" . date('Y-m-d H:i:s') . "' WHERE `id` = '" . (int) $order['id'] . "'"
                )) {
                    throw new RuntimeException('Unable to flag payment mismatch');
                }
            }
            if (!$AZCODE->commit()) {
                throw new RuntimeException('Unable to save bank payment review');
            }
            return 'review';
        }

        $now = date('Y-m-d H:i:s');
        if (!$AZCODE->query(
            "UPDATE `ecom_orders` SET `status` = 'paid', `payment_status` = 'paid',"
            . " `paid_at` = '$now', `updated_at` = '$now' WHERE `id` = '" . (int) $order['id'] . "'"
        )) {
            throw new RuntimeException('Unable to mark the order paid');
        }
        if (!ecom_create_commissions($order)) {
            throw new RuntimeException('Unable to create e-commerce commissions');
        }
        if (!$AZCODE->commit()) {
            throw new RuntimeException('Unable to commit bank payment');
        }
        return 'accepted';
    } catch (Throwable $error) {
        $AZCODE->rollback();
        if ($error instanceof mysqli_sql_exception && (int) $error->getCode() === 1062) {
            return 'duplicate';
        }
        error_log('E-commerce bank payment failed: ' . $error->getMessage());
        return 'error';
    }
}

function ecom_create_commissions($order)
{
    global $AZCODE;
    $orderId = (int) $order['id'];
    $now = date('Y-m-d H:i:s');
    $referralRate = max(0, min(100, (float) ecom_setting('referral_commission_percent', '5')));
    $sellerFeeRate = max(0, min(100, (float) ecom_setting('seller_fee_percent', '5')));

    if (!empty($order['referrer_user_id']) && $referralRate > 0) {
        $referrerId = (int) $order['referrer_user_id'];
        $amount = round((float) $order['subtotal'] * $referralRate / 100, 2);
        if ($amount > 0 && ecom_commission_kind_for_user($referrerId) === 'referral') {
            if (!$AZCODE->insert('ecom_commissions', [
                'commission_key' => 'referral:' . $orderId . ':' . $referrerId,
                'order_id' => $orderId,
                'order_item_id' => 0,
                'ctv_user_id' => $referrerId,
                'kind' => 'referral',
                'amount' => number_format($amount, 2, '.', ''),
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ])) {
                return false;
            }
        }
    }

    foreach ($AZCODE->get_list(
        "SELECT * FROM `ecom_order_items` WHERE `order_id` = '$orderId' AND `seller_user_id` IS NOT NULL"
    ) as $item) {
        $sellerId = (int) $item['seller_user_id'];
        if ($sellerId <= 0 || $sellerFeeRate >= 100 || ecom_commission_kind_for_user($sellerId) !== 'seller') {
            continue;
        }
        $amount = round((float) $item['line_total'] * (100 - $sellerFeeRate) / 100, 2);
        if ($amount <= 0) {
            continue;
        }
        $itemId = (int) $item['id'];
        if (!$AZCODE->insert('ecom_commissions', [
            'commission_key' => 'seller:' . $itemId . ':' . $sellerId,
            'order_id' => $orderId,
            'order_item_id' => $itemId,
            'ctv_user_id' => $sellerId,
            'kind' => 'seller',
            'amount' => number_format($amount, 2, '.', ''),
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now
        ])) {
            return false;
        }
    }
    return true;
}

function ecom_restore_order_stock($orderId)
{
    global $AZCODE;
    $orderId = (int) $orderId;
    foreach ($AZCODE->get_list(
        "SELECT `product_id`,`quantity` FROM `ecom_order_items`"
        . " WHERE `order_id` = '$orderId' AND `product_id` IS NOT NULL"
    ) as $item) {
        $productId = (int) $item['product_id'];
        $quantity = (int) $item['quantity'];
        if (!$AZCODE->query(
            "UPDATE `ecom_products` SET `stock` = `stock` + '$quantity' WHERE `id` = '$productId'"
        )) {
            return false;
        }
    }
    return true;
}

function ecom_expire_unpaid_orders($minutes = 120)
{
    global $AZCODE;
    if (!ecom_is_installed()) {
        return 0;
    }

    $minutes = max(1, (int) $minutes);
    $cutoff = date('Y-m-d H:i:s', time() - ($minutes * 60));
    $cutoffSql = $AZCODE->escape($cutoff);
    $paymentMethodFilter = ecom_supports_cash_on_delivery() ? " AND `payment_method` <> 'cod'" : '';
    $expired = $AZCODE->get_list(
        "SELECT `id` FROM `ecom_orders` WHERE `status` = 'pending_payment'"
        . " AND `payment_status` = 'unpaid'$paymentMethodFilter AND `created_at` < '$cutoffSql'"
        . " ORDER BY `id` ASC LIMIT 100"
    );
    $count = 0;
    foreach ($expired as $candidate) {
        $orderId = (int) $candidate['id'];
        if (!$AZCODE->begin_transaction()) {
            continue;
        }
        try {
            $order = $AZCODE->get_row(
                "SELECT `id`,`status`,`payment_status` FROM `ecom_orders` WHERE `id` = '$orderId' FOR UPDATE"
            );
            if ($order && $order['status'] === 'pending_payment' && $order['payment_status'] === 'unpaid') {
                if (!ecom_restore_order_stock($orderId)) {
                    throw new RuntimeException('Unable to restore stock for expired order ' . $orderId);
                }
                if (!$AZCODE->query(
                    "UPDATE `ecom_orders` SET `status` = 'cancelled', `updated_at` = '"
                    . date('Y-m-d H:i:s') . "' WHERE `id` = '$orderId'"
                )) {
                    throw new RuntimeException('Unable to expire unpaid order ' . $orderId);
                }
                $count++;
            }
            if (!$AZCODE->commit()) {
                throw new RuntimeException('Unable to commit expired order ' . $orderId);
            }
        } catch (Throwable $error) {
            $AZCODE->rollback();
            error_log('E-commerce order expiry failed: ' . $error->getMessage());
        }
    }
    return $count;
}

function ecom_request_payout($userId, $kind = null)
{
    global $AZCODE;
    $userId = (int) $userId;
    $userKind = ecom_commission_kind_for_user($userId);
    if ($userKind === null || ($kind !== null && $kind !== $userKind)) {
        return ['error' => 'Tài khoản không có quyền yêu cầu thanh toán loại hoa hồng này.'];
    }
    $kind = $userKind;
    if (!$AZCODE->begin_transaction()) {
        return ['error' => 'Không thể khởi tạo yêu cầu thanh toán.'];
    }

    try {
        $commissions = $AZCODE->get_list(
            "SELECT c.`id`,c.`amount` FROM `ecom_commissions` c"
            . " LEFT JOIN `ecom_payout_items` pi ON pi.`commission_id` = c.`id`"
            . " WHERE c.`ctv_user_id` = '$userId' AND c.`kind` = '$kind' AND c.`status` = 'approved'"
            . " AND pi.`commission_id` IS NULL FOR UPDATE"
        );
        if (!$commissions) {
            throw new RuntimeException('Chưa có hoa hồng được duyệt để yêu cầu thanh toán.');
        }

        $total = 0.0;
        foreach ($commissions as $commission) {
            $total += (float) $commission['amount'];
        }
        $now = date('Y-m-d H:i:s');
        if (!$AZCODE->insert('ecom_payouts', [
            'ctv_user_id' => $userId,
            'amount' => number_format($total, 2, '.', ''),
            'status' => 'requested',
            'created_at' => $now,
            'updated_at' => $now
        ])) {
            throw new RuntimeException('Không thể tạo yêu cầu thanh toán.');
        }
        $payoutId = (int) $AZCODE->insert_id();
        foreach ($commissions as $commission) {
            if (!$AZCODE->insert('ecom_payout_items', [
                'payout_id' => $payoutId,
                'commission_id' => (int) $commission['id']
            ])) {
                throw new RuntimeException('Một số hoa hồng đã được gắn với yêu cầu thanh toán khác.');
            }
        }

        if (!$AZCODE->commit()) {
            throw new RuntimeException('Không thể lưu yêu cầu thanh toán.');
        }
        return ['payout_id' => $payoutId, 'amount' => $total];
    } catch (Throwable $error) {
        $AZCODE->rollback();
        return ['error' => $error->getMessage()];
    }
}
