<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

if (empty($_SESSION['username'])) {
    header('Location: ' . BASE_URL('Auth/Login'));
    exit();
}
$title = 'ĐƠN HÀNG CỦA TÔI | ' . $AZCODE->site('tenweb');
$orderStatusLabels = [
    'pending_payment' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đang giao',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy',
    'refunded' => 'Đã hoàn tiền',
    'payment_review' => 'Chờ đối soát'
];
$orders = ecom_is_installed() ? $AZCODE->get_list(
    "SELECT * FROM `ecom_orders` WHERE `customer_user_id` = '" . (int) $getUser['id'] . "' ORDER BY `created_at` DESC LIMIT 100"
) : [];
require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<main class="w-full max-w-6xl mx-auto px-3 py-8 text-white">
    <h1 class="text-2xl font-bold mb-5">Đơn hàng của tôi</h1>
    <?php if (!$orders) { ?>
        <p>Bạn chưa có đơn hàng nào. <a class="underline" href="<?=ecom_h(BASE_URL('Shop'));?>">Mua sắm ngay</a></p>
    <?php } else { ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead><tr class="border-b border-gray-500"><th class="p-2">Mã đơn</th><th class="p-2">Ngày</th><th class="p-2">Tổng</th><th class="p-2">Thanh toán</th><th class="p-2">Trạng thái đơn</th><th class="p-2">Theo dõi</th></tr></thead>
                <tbody>
                <?php foreach ($orders as $order) { ?>
                    <tr class="border-b border-gray-700">
                        <td class="p-2"><?=ecom_h($order['order_no']);?></td>
                        <td class="p-2"><?=ecom_h($order['created_at']);?></td>
                        <td class="p-2"><?=number_format((float) $order['total'], 0, ',', '.');?> đ</td>
                        <td class="p-2">
                            <?php if ($order['payment_status'] === 'paid') { ?>
                                <span class="text-green-300">Đã thanh toán</span>
                            <?php } elseif (isset($order['payment_method']) && $order['payment_method'] === 'cod') { ?>
                                <span>COD - thanh toán khi nhận hàng</span>
                            <?php } elseif ($order['payment_status'] === 'mismatch') { ?>
                                <span class="text-yellow-300">Đang đối soát</span>
                            <?php } elseif ($order['payment_status'] === 'refunded') { ?>
                                <span>Đã hoàn tiền</span>
                            <?php } else { ?>
                                <span>Chưa thanh toán</span>
                            <?php } ?>
                        </td>
                        <td class="p-2"><?=ecom_h($orderStatusLabels[$order['status']] ?? 'Đang cập nhật');?></td>
                        <td class="p-2">
                            <?php if ($order['status'] === 'pending_payment' && $order['payment_status'] === 'unpaid') { ?>
                                <a class="underline" href="<?=ecom_h(BASE_URL('Checkout?order=' . rawurlencode($order['order_no'])));?>">Thanh toán / đối soát</a>
                            <?php } elseif ($order['status'] === 'cancelled' && $order['payment_status'] === 'unpaid') { ?>
                                <?=isset($order['payment_method']) && $order['payment_method'] === 'cod' ? 'Đơn COD đã hủy' : 'Liên hệ cửa hàng nếu đã chuyển khoản';?>
                            <?php } elseif ($order['payment_status'] === 'mismatch') { ?>
                                <a class="underline" href="<?=ecom_h(BASE_URL('Checkout?order=' . rawurlencode($order['order_no'])));?>">Xem thông tin đối soát</a>
                            <?php } elseif ($order['tracking_no']) { ?>
                                <?=ecom_h($order['shipping_carrier']);?> · <?=ecom_h($order['tracking_no']);?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</main>
<?php require_once(__DIR__ . "/Footer.php"); ?>
