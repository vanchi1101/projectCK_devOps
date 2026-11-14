<?php

require_once("../config/config.php");
require_once("../config/function.php");
require_once(__DIR__ . '/../includes/ecommerce.php');

/*
 * Cấu hình:
 * api_bank: Token API.
 * stk_bank: Số tài khoản ngân hàng nhận tiền.
 * ck_bank: Phần trăm khuyến mãi nạp tiền.
 */

function acbCronLog($message)
{
    file_put_contents(
        __DIR__ . '/LogBank.txt',
        '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

/*
 * Khóa cron trên cùng máy chủ để tránh hai tiến trình
 * cùng kiểm tra và cộng một giao dịch.
 */
$lockHandle = fopen(__DIR__ . '/acb_cron.lock', 'c');

if ($lockHandle === false) {
    die('Không thể tạo file khóa cron');
}

if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    fclose($lockHandle);
    die('Cron ngân hàng đang chạy');
}

register_shutdown_function(function () use ($lockHandle) {
    if (is_resource($lockHandle)) {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
});

$token = trim((string) $AZCODE->site('api_bank'));
$stk = trim((string) $AZCODE->site('stk_bank'));

if ($token === '') {
    die('Thiếu token API ngân hàng');
}

if ($stk === '') {
    die('Thiếu số tài khoản ngân hàng');
}

$lastCheck = (int) $AZCODE->site('check_time_cron_bank');

if (time() - $lastCheck < 10) {
    die('Không thể cron vào lúc này! Vui lòng chờ đủ 10 giây.');
}

$AZCODE->update(
    "options",
    ['value' => time()],
    " `name` = 'check_time_cron_bank' "
);

/* GỌI API */

$url = 'https://id.xapi.vn/historyapiacb/' . rawurlencode($token);

$response = curl_get($url);

if (!is_string($response) || trim($response) === '') {
    acbCronLog('Lỗi: API không trả về dữ liệu');
    die('API không trả về dữ liệu');
}

$result = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
    acbCronLog('Lỗi: JSON không hợp lệ');
    die('Dữ liệu API không hợp lệ');
}

if (
    (int) ($result['codeStatus'] ?? 0) !== 200 ||
    !isset($result['data']) ||
    !is_array($result['data'])
) {
    acbCronLog('Lỗi: API không trả về danh sách giao dịch thành công');
    die('Lấy dữ liệu giao dịch thất bại');
}

$bonusConfig = $AZCODE->site('ck_bank');

if (
    $bonusConfig !== '' &&
    $bonusConfig !== null &&
    !is_numeric($bonusConfig)
) {
    die('Phần trăm khuyến mãi ngân hàng không hợp lệ');
}

$bonusPercent = (float) $bonusConfig;

if (!is_finite($bonusPercent) || $bonusPercent < 0) {
    die('Phần trăm khuyến mãi ngân hàng không hợp lệ');
}

$credited = 0;
$skipped = 0;
$failed = 0;

try {
    $expiredOrders = ecom_expire_unpaid_orders();
    if ($expiredOrders > 0) {
        acbCronLog("Đã hết hạn $expiredOrders đơn chưa thanh toán");
    }
} catch (Throwable $error) {
    acbCronLog('Lỗi hết hạn đơn thương mại điện tử: ' . $error->getMessage());
}

/* XỬ LÝ GIAO DỊCH */

foreach ($result['data'] as $transaction) {
    if (!is_array($transaction)) {
        $skipped++;
        continue;
    }

    /* Chỉ nhận tiền vào. */
    $type = strtoupper(trim((string) ($transaction['type'] ?? '')));

    if ($type !== 'IN') {
        $skipped++;
        continue;
    }

    /* Chỉ xử lý đúng tài khoản đã cấu hình. */
    $account = trim((string) ($transaction['account'] ?? ''));

    if ($account !== $stk) {
        $skipped++;
        continue;
    }

    $rawAmount = $transaction['amount'] ?? null;

    if (!is_numeric($rawAmount)) {
        $skipped++;
        continue;
    }

    $amount = (float) $rawAmount;

    if (
        !is_finite($amount) ||
        $amount <= 0 ||
        floor($amount) !== $amount
    ) {
        $skipped++;
        continue;
    }

    $des = trim((string) ($transaction['description'] ?? ''));

    $tid = '';

    foreach (['transactionID', 'transactionNumber', 'id'] as $key) {
        $candidate = $transaction[$key] ?? null;

        if (is_string($candidate) && trim($candidate) !== '') {
            $tid = trim($candidate);
            break;
        }
    }

    /*
     * Mã giao dịch của JSON mẫu là chuỗi hex.
     * Chỉ chấp nhận ký tự an toàn trước khi dùng trong SQL.
     */
    if (!preg_match('/^[a-zA-Z0-9_-]{1,128}$/D', $tid)) {
        $skipped++;
        continue;
    }

    /*
     * Dùng hàm có sẵn của website.
     * Hàm cần nhận được ID từ nội dung như "naptien 8 ...".
     */
    $parsedId = parse_order_id($des);

    $id = filter_var(
        $parsedId,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $log = json_encode([
        'tid' => $tid,
        'description' => $des,
        'user_id' => $id ?: null,
        'amount' => $amount
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($log !== false) {
        acbCronLog($log);
    }

    $ecommercePayment = ecom_process_bank_payment($tid, $des, $amount);
    if ($ecommercePayment !== 'not_ecommerce') {
        if ($ecommercePayment === 'accepted') {
            $credited++;
            acbCronLog("Đã thanh toán đơn hàng thương mại điện tử: order_no=" . ecom_parse_order_no($des) . ", tid=$tid");
        } elseif (in_array($ecommercePayment, ['duplicate', 'review', 'unknown_order'], true)) {
            $skipped++;
            acbCronLog("Đối soát đơn hàng cần kiểm tra: result=$ecommercePayment, tid=$tid");
        } else {
            $failed++;
            acbCronLog("Lỗi xử lý thanh toán đơn hàng: result=$ecommercePayment, tid=$tid");
        }
        continue;
    }

    if ($id === false) {
        $skipped++;
        continue;
    }

    $id = (int) $id;

    /* Bỏ qua giao dịch đã ghi nhận. */
    $exists = $AZCODE->num_rows(
        "SELECT * FROM `bank_auto` WHERE `tid` = '$tid'"
    );

    if ($exists > 0) {
        $skipped++;
        continue;
    }

    $row = $AZCODE->get_row(
        "SELECT * FROM `users` WHERE `id` = '$id'"
    );

    if (!$row || empty($row['username'])) {
        acbCronLog("Bỏ qua: không tìm thấy user ID $id, tid=$tid");
        $skipped++;
        continue;
    }

    /* Làm tròn số tiền sau khuyến mãi đến đơn vị VND. */
    $realAmount = round(
        $amount + ($amount * $bonusPercent / 100),
        0
    );

    if (!is_finite($realAmount) || $realAmount <= 0) {
        acbCronLog("Lỗi: số tiền sau khuyến mãi không hợp lệ, tid=$tid");
        $failed++;
        continue;
    }

    /* GHI NHẬN GIAO DỊCH */

    $create = $AZCODE->insert("bank_auto", [
        'tid' => $tid,
        'description' => $des,
        'amount' => $amount,
        'time' => gettime(),
        'username' => $row['username']
    ]);

    if (!$create) {
        acbCronLog("Lỗi: không thể ghi bank_auto, tid=$tid");
        $failed++;
        continue;
    }

    /* CỘNG SỐ DƯ */

    $isCheckMoney = $AZCODE->cong(
        "users",
        "money",
        $realAmount,
        " `id` = '$id' "
    );

    if (!$isCheckMoney) {
        /*
         * Giữ bản ghi bank_auto để tránh tự động cộng lại
         * khi chưa xác định được kết quả cập nhật số dư.
         */
        acbCronLog(
            "CẦN ĐỐI SOÁT: đã ghi bank_auto nhưng cập nhật money báo lỗi"
            . ", tid=$tid, user_id=$id, amount=$realAmount"
        );

        $failed++;
        continue;
    }

    /* CỘNG TỔNG TIỀN NẠP */

    $updateTotal = $AZCODE->cong(
        "users",
        "total_money",
        $realAmount,
        " `id` = '$id' "
    );

    if (!$updateTotal) {
        acbCronLog(
            "CẦN ĐỐI SOÁT: đã cộng money nhưng cập nhật total_money báo lỗi"
            . ", tid=$tid, user_id=$id"
        );
    }

    /* GHI LỊCH SỬ DÒNG TIỀN */

    $moneyBefore = (float) $row['money'];

    $insertHistory = $AZCODE->insert("dongtien", [
        'sotientruoc' => $moneyBefore,
        'sotienthaydoi' => $realAmount,
        'sotiensau' => $moneyBefore + $realAmount,
        'thoigian' => gettime(),
        'noidung' => 'Nạp tiền tự động ngân hàng (ACB | ' . $tid . ')',
        'username' => $row['username']
    ]);

    if (!$insertHistory) {
        acbCronLog(
            "CẦN ĐỐI SOÁT: đã cộng money nhưng chưa ghi được dongtien"
            . ", tid=$tid, user_id=$id"
        );
    }

    acbCronLog(
        "Đã cộng tiền: tid=$tid, user_id=$id"
        . ", tien_goc=$amount, thuc_nhan=$realAmount"
    );

    $credited++;
}

echo 'Cron ACB hoàn tất'
    . ' | Đã cộng: ' . $credited
    . ' | Bỏ qua: ' . $skipped
    . ' | Lỗi: ' . $failed;