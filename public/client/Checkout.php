<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

if (empty($_SESSION['username'])) {
    header('Location: ' . BASE_URL('Auth/Login'));
    exit();
}

$title = 'THANH TOÁN ĐƠN HÀNG | ' . $AZCODE->site('tenweb');
$isInstalled = ecom_is_installed();
$supportsCod = $isInstalled && ecom_supports_cash_on_delivery();
if ($isInstalled) {
    ecom_expire_unpaid_orders();
}
$message = '';
$order = false;
$orderNo = trim((string) ($_GET['order'] ?? ''));

if ($isInstalled && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } else {
        $result = ecom_create_order($getUser, $_POST);
        if (isset($result['order_no'])) {
            header('Location: ' . BASE_URL('Checkout?order=' . rawurlencode($result['order_no'])));
            exit();
        }
        $message = $result['error'] ?? 'Không thể tạo đơn hàng.';
    }
}

if ($isInstalled && $orderNo !== '' && preg_match('/^EC[A-F0-9]{24}$/i', $orderNo)) {
    $safeOrderNo = $AZCODE->escape(strtoupper($orderNo));
    $order = $AZCODE->get_row(
        "SELECT * FROM `ecom_orders` WHERE `order_no` = '$safeOrderNo'"
        . " AND `customer_user_id` = '" . (int) $getUser['id'] . "'"
    );
}
$items = !$order && $isInstalled ? ecom_cart_items() : [];
$provinces = ecom_vietnam_provinces();
$cartSubtotal = 0;
foreach ($items as $item) {
    $cartSubtotal += (float) $item['line_total'];
}
$shippingFee = $isInstalled ? max(0, (float) ecom_setting('shipping_fee', '0')) : 0;
$checkoutTotal = $cartSubtotal + $shippingFee;
$bank = $order ? $AZCODE->get_row("SELECT * FROM `bank` LIMIT 1") : false;
$awaitingPayment = $order
    && (!isset($order['payment_method']) || $order['payment_method'] === 'bank_transfer')
    && $order['status'] === 'pending_payment'
    && $order['payment_status'] === 'unpaid';
$isCodOrder = $order && isset($order['payment_method']) && $order['payment_method'] === 'cod';
$qrUrl = '';
if ($awaitingPayment && $bank) {
    $bankCode = rawurlencode(strtoupper((string) $bank['name']));
    $accountNumber = rawurlencode((string) $bank['stk']);
    $accountName = rawurlencode((string) $bank['bank_name']);
    $info = rawurlencode($order['order_no']);
    $amount = (int) round((float) $order['total']);
    $qrUrl = "https://img.vietqr.io/image/$bankCode-$accountNumber-compact2.png"
        . "?amount=$amount&addInfo=$info&accountName=$accountName";
}

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<style>
    .checkout-page { min-height: 75vh; padding: clamp(24px, 5vw, 52px) 16px 64px; color: #eaf2fb; }
    .checkout-container { width: min(1120px, 100%); margin: 0 auto; }
    .checkout-header { margin-bottom: 24px; }
    .checkout-kicker { margin: 0 0 8px; color: #8de3e6; font-size: .78rem; font-weight: 850; letter-spacing: .13em; text-transform: uppercase; }
    .checkout-title { margin: 0; color: #fff; font-size: clamp(1.9rem, 4vw, 2.6rem); font-weight: 850; }
    .checkout-intro { margin: 8px 0 0; color: #bfd0e2; }
    .checkout-error, .checkout-info {
        margin-bottom: 17px;
        padding: 14px 17px;
        border-radius: 13px;
        line-height: 1.55;
    }
    .checkout-error { border: 1px solid #fca5a5; background: #fff1f2; color: #9f1239; }
    .checkout-info { border: 1px solid rgba(147,197,253,.55); background: rgba(30,64,110,.7); color: #e6f2ff; }
    .checkout-layout { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr); gap: 22px; align-items: start; }
    .checkout-panel {
        overflow: hidden;
        border: 1px solid #e0e8f1;
        border-radius: 19px;
        background: #fff;
        color: #172b46;
        box-shadow: 0 18px 42px rgba(0,0,0,.15);
    }
    .checkout-panel-heading { padding: 20px 22px 16px; border-bottom: 1px solid #e8eef5; }
    .checkout-panel-heading h2 { margin: 0; color: #142640; font-size: 1.18rem; font-weight: 850; }
    .checkout-panel-heading p { margin: 6px 0 0; color: #718198; font-size: .88rem; line-height: 1.5; }
    .checkout-panel-body { padding: 21px 22px; }
    .checkout-fields { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 16px; }
    .checkout-field { display: grid; min-width: 0; gap: 7px; color: #344a63; font-size: .88rem; font-weight: 750; }
    .checkout-field-full { grid-column: 1 / -1; }
    .checkout-field input, .checkout-field select, .checkout-field textarea {
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid #cbd8e6;
        border-radius: 10px;
        outline: none;
        background: #f9fbfd;
        color: #172b46;
        font: inherit;
        font-size: .94rem;
        font-weight: 450;
    }
    .checkout-field textarea { min-height: 88px; resize: vertical; }
    .checkout-field input:focus, .checkout-field select:focus, .checkout-field textarea:focus {
        border-color: #3182ce;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(49,130,206,.13);
    }
    .checkout-field-hint { color: #718198; font-size: .78rem; font-weight: 450; line-height: 1.45; }
    .checkout-summary { position: sticky; top: 95px; }
    .checkout-summary-body { padding: 8px 22px 20px; }
    .checkout-item { display: flex; justify-content: space-between; gap: 14px; padding: 14px 0; border-bottom: 1px solid #edf1f5; }
    .checkout-item-name { color: #263d57; font-size: .92rem; font-weight: 700; line-height: 1.45; }
    .checkout-item-quantity { display: inline-block; margin-left: 5px; color: #718198; font-size: .84rem; font-weight: 600; }
    .checkout-item-price { flex-shrink: 0; color: #263d57; font-size: .92rem; font-weight: 800; }
    .checkout-totals { display: grid; gap: 11px; padding-top: 16px; }
    .checkout-total-line { display: flex; justify-content: space-between; gap: 12px; color: #64748b; font-size: .91rem; }
    .checkout-total-line strong { color: #344a63; }
    .checkout-grand-total { display: flex; justify-content: space-between; gap: 12px; margin-top: 5px; padding-top: 15px; border-top: 1px solid #dfe7ef; color: #142640; font-size: 1.12rem; font-weight: 850; }
    .checkout-grand-total strong { color: #1263a5; }
    .checkout-payment-hint { margin: 17px 0 0; padding: 12px; border-radius: 11px; background: #f0f7fc; color: #52657e; font-size: .84rem; line-height: 1.5; }
    .checkout-submit {
        display: block;
        width: 100%;
        min-height: 49px;
        margin-top: 17px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(115deg, #1677d2, #0d8c9b);
        color: #fff;
        font-size: .98rem;
        font-weight: 850;
        cursor: pointer;
        box-shadow: 0 9px 20px rgba(22,119,210,.2);
    }
    .checkout-submit:hover { filter: brightness(1.05); }
    .checkout-state-card { max-width: 760px; padding: clamp(20px, 4vw, 32px); }
    .checkout-state-meta { display: grid; grid-template-columns: 1fr auto; gap: 10px 20px; color: #64748b; }
    .checkout-state-meta strong { color: #172b46; text-align: right; overflow-wrap: anywhere; }
    .checkout-paid, .checkout-review, .checkout-cancelled, .checkout-pending {
        margin-top: 20px;
        padding: 16px;
        border: 1px solid;
        border-radius: 13px;
        line-height: 1.6;
    }
    .checkout-paid { border-color: #86efac; background: #f0fdf4; color: #166534; }
    .checkout-review, .checkout-pending { border-color: #fcd34d; background: #fffbeb; color: #92400e; }
    .checkout-cancelled { border-color: #fda4af; background: #fff1f2; color: #9f1239; }
    .checkout-state-title { margin: 0 0 5px; font-size: 1.12rem; font-weight: 850; }
    .checkout-paid a { display: inline-block; margin-top: 8px; color: #166534; font-weight: 800; }
    .checkout-order-number { margin: 16px 0; padding: 12px; border: 1px dashed #bfd0e2; border-radius: 10px; background: #f8fafc; color: #173d70; font-weight: 850; overflow-wrap: anywhere; }
    .checkout-bank-details { display: grid; gap: 7px; color: #52657e; font-size: .92rem; }
    .checkout-bank-details strong { color: #172b46; }
    .checkout-qr { display: block; width: min(300px, 100%); margin-top: 18px; padding: 9px; border: 1px solid #e2e8f0; border-radius: 13px; background: #fff; }
    .checkout-payment-note { margin: 14px 0 0; color: #718198; font-size: .86rem; line-height: 1.5; }
    .checkout-no-items { padding: 25px; border-radius: 16px; background: #fff; color: #52657e; text-align: center; }
    .checkout-no-items a { color: #1768a6; font-weight: 800; }
    @media (max-width: 780px) {
        .checkout-page { padding: 24px 12px 46px; }
        .checkout-layout { grid-template-columns: 1fr; }
        .checkout-summary { position: static; }
    }
    @media (max-width: 520px) {
        .checkout-panel-heading, .checkout-panel-body { padding-right: 16px; padding-left: 16px; }
        .checkout-fields { grid-template-columns: 1fr; gap: 13px; }
        .checkout-field-full { grid-column: auto; }
        .checkout-state-meta { grid-template-columns: 1fr; gap: 4px; }
        .checkout-state-meta strong { text-align: left; }
    }
</style>

<main class="checkout-page">
    <div class="checkout-container">
        <header class="checkout-header">
            <p class="checkout-kicker">Đặt hàng an toàn</p>
            <h1 class="checkout-title"><?= $order ? 'Thông tin thanh toán' : 'Hoàn tất đơn hàng'; ?></h1>
            <p class="checkout-intro">Nhập chính xác thông tin nhận hàng để cửa hàng giao sản phẩm đến bạn.</p>
        </header>
        <?php if ($message !== '') { ?><div class="checkout-error" role="alert"><?=ecom_h($message);?></div><?php } ?>

        <?php if (!$isInstalled) { ?>
            <div class="checkout-no-items">Cửa hàng điện tử chưa được khởi tạo. Vui lòng liên hệ quản trị viên.</div>
        <?php } elseif ($order) { ?>
            <?php
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
            ?>
            <section class="checkout-panel checkout-state-card">
                <div class="checkout-panel-heading">
                    <h2>Theo dõi thanh toán</h2>
                    <p>Đơn hàng và khoản thanh toán được đối soát riêng theo mã đơn.</p>
                </div>
                <div class="checkout-panel-body">
                    <div class="checkout-state-meta">
                        <span>Mã đơn hàng</span><strong><?=ecom_h($order['order_no']);?></strong>
                        <span>Trạng thái</span><strong><?=ecom_h($orderStatusLabels[$order['status']] ?? 'Đang cập nhật');?></strong>
                        <span>Phương thức thanh toán</span><strong><?=$isCodOrder ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng';?></strong>
                        <span>Tổng thanh toán</span><strong><?=number_format((float) $order['total'], 0, ',', '.');?> đ</strong>
                    </div>
                    <?php if ($order['payment_status'] === 'paid') { ?>
                        <div class="checkout-paid" role="status" aria-live="polite">
                            <p class="checkout-state-title">Thanh toán thành công!</p>
                            Cửa hàng đã nhận được tiền cho đơn hàng này.
                            <a href="<?=ecom_h(BASE_URL('MyOrders'));?>">Theo dõi đơn hàng của tôi</a>
                        </div>
                    <?php } elseif ($order['status'] === 'payment_review' || $order['payment_status'] === 'mismatch') { ?>
                        <div class="checkout-review" role="status">
                            <p class="checkout-state-title">Giao dịch đang được kiểm tra</p>
                            Vui lòng liên hệ cửa hàng nếu trạng thái chưa được cập nhật.
                        </div>
                    <?php } elseif ($order['status'] === 'cancelled') { ?>
                        <div class="checkout-cancelled" role="status">
                            <p class="checkout-state-title">Đơn hàng đã bị hủy</p>
                            <?php if ($isCodOrder) { ?>
                                Bạn sẽ không bị thu tiền COD. Vui lòng liên hệ cửa hàng nếu cần được hỗ trợ.
                            <?php } else { ?>
                                Vui lòng không chuyển khoản. Nếu bạn đã thanh toán, hãy liên hệ cửa hàng và cung cấp mã đơn.
                            <?php } ?>
                        </div>
                    <?php } elseif ($isCodOrder && $order['payment_status'] === 'unpaid') { ?>
                        <div class="checkout-pending" role="status">
                            <p class="checkout-state-title">Đơn hàng thanh toán khi nhận hàng</p>
                            Bạn sẽ thanh toán <?=number_format((float) $order['total'], 0, ',', '.');?> đ cho nhân viên giao hàng khi nhận sản phẩm.
                            <?php if ($order['status'] === 'shipped' && $order['tracking_no']) { ?>
                                <br>Mã vận đơn: <strong><?=ecom_h($order['tracking_no']);?></strong>
                            <?php } ?>
                        </div>
                    <?php } elseif ($awaitingPayment && $bank && $qrUrl !== '') { ?>
                        <div class="checkout-pending" role="status" aria-live="polite">
                            <p class="checkout-state-title">Đang chờ ngân hàng xác nhận</p>
                            Trang sẽ tự động kiểm tra lại sau mỗi 10 giây.
                        </div>
                        <div class="checkout-order-number"><?=ecom_h($order['order_no']);?></div>
                        <div class="checkout-bank-details">
                            <span>Ngân hàng: <strong><?=ecom_h($bank['name']);?></strong></span>
                            <span>Số tài khoản: <strong><?=ecom_h($bank['stk']);?></strong></span>
                            <span>Chủ tài khoản: <strong><?=ecom_h($bank['bank_name']);?></strong></span>
                            <span>Nội dung chuyển khoản: <strong><?=ecom_h($order['order_no']);?></strong></span>
                            <span>Số tiền chính xác: <strong><?=number_format((float) $order['total'], 0, ',', '.');?> đ</strong></span>
                        </div>
                        <img src="<?=ecom_h($qrUrl);?>" alt="Mã QR thanh toán đơn hàng" class="checkout-qr">
                        <p class="checkout-payment-note">
                            Hãy chuyển đúng số tiền và giữ nguyên mã đơn trong nội dung chuyển khoản.
                        </p>
                    <?php } elseif ($awaitingPayment && !$bank) { ?>
                        <div class="checkout-review">Chưa cấu hình tài khoản nhận tiền. Vui lòng liên hệ cửa hàng.</div>
                    <?php } else { ?>
                        <div class="checkout-review">Vui lòng liên hệ cửa hàng để được hỗ trợ về trạng thái đơn hàng.</div>
                    <?php } ?>
                </div>
            </section>
        <?php } elseif (!$items) { ?>
            <div class="checkout-no-items">Giỏ hàng đang trống. <a href="<?=ecom_h(BASE_URL('Shop'));?>">Tiếp tục mua sắm</a></div>
        <?php } else { ?>
            <form method="post" class="checkout-layout">
                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                <section class="checkout-panel">
                    <div class="checkout-panel-heading">
                        <h2>Thông tin nhận hàng</h2>
                        <p>Các mục có dấu * là bắt buộc. Chọn tỉnh/thành theo danh sách hiện hành.</p>
                    </div>
                    <div class="checkout-panel-body">
                        <div class="checkout-fields">
                            <label class="checkout-field">Họ tên người nhận *
                                <input required maxlength="190" name="customer_name" autocomplete="name"
                                       value="<?=ecom_h($_POST['customer_name'] ?? $getUser['username']);?>">
                            </label>
                            <label class="checkout-field">Số điện thoại *
                                <input required type="tel" maxlength="40" name="customer_phone" autocomplete="tel"
                                       value="<?=ecom_h($_POST['customer_phone'] ?? '');?>">
                            </label>
                            <label class="checkout-field checkout-field-full">Email (không bắt buộc)
                                <input type="email" maxlength="190" name="customer_email" autocomplete="email"
                                       value="<?=ecom_h($_POST['customer_email'] ?? ($getUser['email'] ?? ''));?>">
                            </label>
                            <label class="checkout-field checkout-field-full">Tỉnh / thành phố *
                                <select required name="shipping_city" autocomplete="address-level1">
                                    <option value="">Chọn tỉnh / thành phố</option>
                                    <?php foreach ($provinces as $province) { ?>
                                        <option value="<?=ecom_h($province);?>" <?=((string) ($_POST['shipping_city'] ?? '') === $province) ? 'selected' : '';?>>
                                            <?=ecom_h($province);?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </label>
                            <label class="checkout-field checkout-field-full">Địa chỉ chi tiết *
                                <textarea required maxlength="500" name="shipping_address" autocomplete="street-address"
                                          placeholder="Số nhà, tên đường, phường / xã"><?=ecom_h($_POST['shipping_address'] ?? '');?></textarea>
                                <span class="checkout-field-hint">Ghi rõ phường/xã, số nhà và tên đường. Địa chỉ hiện dùng mô hình hành chính 2 cấp.</span>
                            </label>
                            <label class="checkout-field checkout-field-full">Ghi chú giao hàng
                                <textarea name="shipping_note" maxlength="500" placeholder="Thời gian nhận hàng hoặc lưu ý cho người giao"><?=ecom_h($_POST['shipping_note'] ?? '');?></textarea>
                            </label>
                            <fieldset class="checkout-field checkout-field-full" style="border:0;padding:0;margin:0">
                                <legend style="margin-bottom:8px">Phương thức thanh toán *</legend>
                                <label style="display:flex;align-items:flex-start;gap:9px;padding:12px;border:1px solid #cbd8e6;border-radius:10px;background:#f9fbfd;font-weight:600">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                           <?=((string) ($_POST['payment_method'] ?? 'bank_transfer') !== 'cod') ? 'checked' : '';?>>
                                    <span>Chuyển khoản ngân hàng <small style="display:block;color:#718198;font-weight:450">Thanh toán trước khi cửa hàng xử lý giao hàng.</small></span>
                                </label>
                                <?php if ($supportsCod) { ?>
                                    <label style="display:flex;align-items:flex-start;gap:9px;margin-top:8px;padding:12px;border:1px solid #cbd8e6;border-radius:10px;background:#f9fbfd;font-weight:600">
                                        <input type="radio" name="payment_method" value="cod"
                                               <?=((string) ($_POST['payment_method'] ?? '') === 'cod') ? 'checked' : '';?>>
                                        <span>Thanh toán khi nhận hàng (COD) <small style="display:block;color:#718198;font-weight:450">Thanh toán cho nhân viên giao hàng khi nhận sản phẩm.</small></span>
                                    </label>
                                <?php } ?>
                            </fieldset>
                        </div>
                    </div>
                </section>

                <aside class="checkout-panel checkout-summary">
                    <div class="checkout-panel-heading">
                        <h2>Đơn hàng của bạn</h2>
                        <p><?=count($items);?> loại sản phẩm</p>
                    </div>
                    <div class="checkout-summary-body">
                        <?php foreach ($items as $item) { ?>
                            <div class="checkout-item">
                                <span class="checkout-item-name">
                                    <?=ecom_h($item['name']);?>
                                    <span class="checkout-item-quantity">× <?=(int) $item['quantity'];?></span>
                                </span>
                                <strong class="checkout-item-price"><?=number_format((float) $item['line_total'], 0, ',', '.');?> đ</strong>
                            </div>
                        <?php } ?>
                        <div class="checkout-totals">
                            <div class="checkout-total-line"><span>Tạm tính</span><strong><?=number_format($cartSubtotal, 0, ',', '.');?> đ</strong></div>
                            <div class="checkout-total-line"><span>Phí giao hàng</span><strong><?=number_format($shippingFee, 0, ',', '.');?> đ</strong></div>
                            <div class="checkout-grand-total"><span>Tổng thanh toán</span><strong><?=number_format($checkoutTotal, 0, ',', '.');?> đ</strong></div>
                        </div>
                        <p class="checkout-payment-hint">Chọn chuyển khoản hoặc thanh toán khi nhận hàng (COD) trước khi tạo đơn.</p>
                        <button class="checkout-submit" type="submit">Đặt hàng</button>
                    </div>
                </aside>
            </form>
        <?php } ?>
    </div>
</main>
<?php if ($awaitingPayment) { ?>
    <script>
        window.setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
<?php } ?>
<?php require_once(__DIR__ . "/Footer.php"); ?>
