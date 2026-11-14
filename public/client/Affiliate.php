<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

if (empty($_SESSION['username'])) {
    header('Location: ' . BASE_URL('Auth/Login'));
    exit();
}

$commissionKind = ecom_commission_kind_for_user($getUser['id']);
if ($commissionKind !== 'referral') {
    header('Location: ' . BASE_URL($commissionKind === 'seller' ? 'public/ctv/Products.php' : 'Shop'));
    exit();
}

$title = 'TIẾP THỊ LIÊN KẾT | ' . $AZCODE->site('tenweb');
$isInstalled = ecom_is_installed();
$message = '';
$userId = (int) $getUser['id'];

if ($isInstalled && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } elseif (($_POST['action'] ?? '') === 'request_payout') {
        $payout = ecom_request_payout($userId, 'referral');
        $message = isset($payout['error'])
            ? $payout['error']
            : 'Đã gửi yêu cầu thanh toán hoa hồng #' . (int) $payout['payout_id'] . '.';
    }
}

$commissions = $isInstalled ? $AZCODE->get_list(
    "SELECT * FROM `ecom_commissions` WHERE `ctv_user_id` = '$userId' AND `kind` = 'referral'"
    . " ORDER BY `created_at` DESC LIMIT 100"
) : [];
$clickStats = $isInstalled ? $AZCODE->get_row(
    "SELECT `click_count` FROM `ecom_referral_clicks` WHERE `affiliate_user_id` = '$userId'"
) : false;
$availablePayout = $isInstalled ? $AZCODE->get_row(
    "SELECT COALESCE(SUM(c.`amount`),0) AS `total` FROM `ecom_commissions` c"
    . " LEFT JOIN `ecom_payout_items` pi ON pi.`commission_id` = c.`id`"
    . " WHERE c.`ctv_user_id` = '$userId' AND c.`kind` = 'referral' AND c.`status` = 'approved'"
    . " AND pi.`commission_id` IS NULL"
) : false;
$paidCommission = $isInstalled ? $AZCODE->get_row(
    "SELECT COALESCE(SUM(`amount`),0) AS `total` FROM `ecom_commissions`"
    . " WHERE `ctv_user_id` = '$userId' AND `kind` = 'referral' AND `status` = 'paid'"
) : false;
$referredMembers = $isInstalled ? $AZCODE->get_row(
    "SELECT COUNT(*) AS `total` FROM `users` WHERE `ref` = '$userId'"
) : false;
$payouts = $isInstalled ? $AZCODE->get_list(
    "SELECT * FROM `ecom_payouts` WHERE `ctv_user_id` = '$userId'"
    . " ORDER BY `created_at` DESC LIMIT 50"
) : [];
$commissionRate = max(0, min(100, (float) ecom_setting('referral_commission_percent', '5')));
$commissionExample = round(100000 * $commissionRate / 100);
$referralUrl = trim((string) ($getUser['token'] ?? '')) !== ''
    ? BASE_URL('Shop?ref=' . rawurlencode($getUser['token']))
    : '';
$commissionStatusLabels = ['pending' => 'Đang chờ duyệt', 'approved' => 'Đã duyệt', 'paid' => 'Đã thanh toán'];
$payoutStatusLabels = ['requested' => 'Đang xử lý', 'approved' => 'Đã duyệt', 'paid' => 'Đã thanh toán', 'rejected' => 'Từ chối'];

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<style>
    .affiliate-page { flex: 1 0 auto; padding: 28px 16px 48px; color: #172033; background: #f1f5f9; }
    .affiliate-container { max-width: 1560px; margin: 0 auto; }
    .affiliate-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px; }
    .affiliate-card { overflow: hidden; border-radius: 9px; background: #fff; box-shadow: 0 1px 5px rgba(15, 23, 42, .07); }
    .affiliate-card-header { padding: 25px 30px; border-bottom: 1px solid #e8edf3; font-size: 24px; font-weight: 500; }
    .affiliate-card-body { padding: 25px 30px; }
    .affiliate-available-label { margin: 0; color: #475569; font-size: 16px; font-weight: 600; }
    .affiliate-available-value { margin: 0 0 24px; color: #16c45b; font-size: 30px; font-weight: 700; }
    .affiliate-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
    .affiliate-stat { min-height: 126px; padding: 27px 30px; border-radius: 11px; background: #f3f4f6; }
    .affiliate-stat-label { margin: 0 0 4px; color: #475569; font-weight: 600; }
    .affiliate-stat-value { margin: 0; color: #0f172a; font-size: 29px; font-weight: 700; line-height: 1.15; }
    .affiliate-actions { display: flex; flex-direction: column; align-items: flex-start; gap: 4px; margin-top: 21px; }
    .affiliate-actions a { color: #ff3838; font-weight: 600; text-decoration: none; }
    .affiliate-actions a:hover { text-decoration: underline; }
    .affiliate-link-row { display: flex; width: 100%; margin-bottom: 24px; }
    .affiliate-link-input { flex: 1; min-width: 0; padding: 14px 15px; border: 1px solid #d5deea; border-right: 0; border-radius: 7px 0 0 7px; color: #111827; font-weight: 600; }
    .affiliate-copy-button { width: 50px; border: 1px solid #d5deea; border-radius: 0 7px 7px 0; background: white; color: #718198; cursor: pointer; }
    .affiliate-description { color: #2377bd; font-weight: 600; line-height: 2.25; }
    .affiliate-description p { margin: 0 0 13px; }
    .affiliate-subsection { margin-top: 18px; padding: 24px 28px; border-radius: 9px; background: white; box-shadow: 0 1px 5px rgba(15, 23, 42, .07); }
    .affiliate-subsection h2 { margin: 0 0 16px; font-size: 21px; font-weight: 600; }
    .affiliate-table { width: 100%; border-collapse: collapse; }
    .affiliate-table th, .affiliate-table td { padding: 12px; border-bottom: 1px solid #e8edf3; text-align: left; }
    .affiliate-table th { color: #475569; font-size: 14px; }
    .affiliate-request-button { padding: 10px 16px; border: 0; border-radius: 6px; background: #16834a; color: white; font-weight: 600; cursor: pointer; }
    .affiliate-request-button:disabled { background: #94a3b8; cursor: not-allowed; }
    .affiliate-alert { margin-bottom: 16px; padding: 12px 16px; border: 1px solid #93c5fd; border-radius: 7px; background: #eff6ff; color: #1e3a8a; }
    .affiliate-empty { color: #64748b; }
    @media (max-width: 900px) { .affiliate-grid { grid-template-columns: 1fr; } }
    @media (max-width: 520px) {
        .affiliate-page { padding: 18px 10px 36px; }
        .affiliate-card-header { padding: 20px; font-size: 21px; }
        .affiliate-card-body { padding: 20px; }
        .affiliate-stats { gap: 12px; }
        .affiliate-stat { min-height: 105px; padding: 18px; }
        .affiliate-stat-value { font-size: 24px; }
        .affiliate-subsection { padding: 18px; }
    }
</style>

<main class="affiliate-page">
    <div class="affiliate-container">
        <?php if (!$isInstalled) { ?>
            <div class="affiliate-alert">Tính năng thương mại điện tử chưa được khởi tạo.</div>
        <?php } else { ?>
            <?php if ($message !== '') { ?><div class="affiliate-alert"><?=ecom_h($message);?></div><?php } ?>
            <div class="affiliate-grid">
                <section class="affiliate-card">
                    <header class="affiliate-card-header">Thống Kê Của Bạn</header>
                    <div class="affiliate-card-body">
                        <p class="affiliate-available-label">Số tiền hoa hồng khả dụng</p>
                        <p class="affiliate-available-value"><?=number_format((float) ($availablePayout['total'] ?? 0), 0, ',', '.');?>đ</p>
                        <div class="affiliate-stats">
                            <div class="affiliate-stat">
                                <p class="affiliate-stat-label">Số lần nhấp vào liên kết</p>
                                <p class="affiliate-stat-value"><?=number_format((int) ($clickStats['click_count'] ?? 0));?></p>
                            </div>
                            <div class="affiliate-stat">
                                <p class="affiliate-stat-label">Tỷ lệ hoa hồng</p>
                                <p class="affiliate-stat-value"><?=rtrim(rtrim(number_format($commissionRate, 2, '.', ''), '0'), '.');?>%</p>
                            </div>
                            <div class="affiliate-stat">
                                <p class="affiliate-stat-label">Tổng số tiền hoa hồng đã nhận</p>
                                <p class="affiliate-stat-value"><?=number_format((float) ($paidCommission['total'] ?? 0), 0, ',', '.');?>đ</p>
                            </div>
                            <div class="affiliate-stat">
                                <p class="affiliate-stat-label">Thành viên đã giới thiệu</p>
                                <p class="affiliate-stat-value"><?=number_format((int) ($referredMembers['total'] ?? 0));?></p>
                            </div>
                        </div>
                        <nav class="affiliate-actions" aria-label="Thao tác tiếp thị liên kết">
                            <a href="#affiliate-commissions">Xem lịch sử hoa hồng</a>
                            <a href="#affiliate-payouts">Rút hoa hồng về ngân hàng</a>
                        </nav>
                    </div>
                </section>

                <section class="affiliate-card">
                    <header class="affiliate-card-header">Liên Kết Giới Thiệu Của Bạn</header>
                    <div class="affiliate-card-body">
                        <?php if ($isInstalled && $referralUrl !== '') { ?>
                            <div class="affiliate-link-row">
                                <input id="affiliate-link" class="affiliate-link-input" readonly value="<?=ecom_h($referralUrl);?>" aria-label="Liên kết giới thiệu">
                                <button id="copy-affiliate-link" class="affiliate-copy-button" type="button" aria-label="Sao chép liên kết" title="Sao chép">
                                    <i class="fa fa-copy" aria-hidden="true"></i>
                                </button>
                            </div>
                            <p id="copy-message" class="affiliate-empty" aria-live="polite"></p>
                        <?php } else { ?>
                            <div class="affiliate-alert">Tài khoản chưa có mã liên kết. Vui lòng liên hệ quản trị viên.</div>
                        <?php } ?>
                        <div class="affiliate-description">
                            <p>Chia sẻ liên kết này lên mạng xã hội hoặc với bạn bè của bạn.</p>
                            <p>Bạn sẽ nhận được hoa hồng khi người được bạn giới thiệu mua sản phẩm tại <?=ecom_h($AZCODE->site('tenweb')); ?>.</p>
                            <p>Nếu khách mua hàng trị giá 100.000đ, hoa hồng tạm tính theo mức hiện tại là <?=number_format($commissionExample, 0, ',', '.');?>đ (<?=rtrim(rtrim(number_format($commissionRate, 2, '.', ''), '0'), '.');?>%).</p>
                            <p>Hoa hồng được duyệt sau khi đơn hàng hoàn tất và không bị hoàn/hủy.</p>
                            <p>Không tự giới thiệu đơn hàng của chính mình để nhận hoa hồng; đơn cùng tài khoản sẽ không được tính.</p>
                        </div>
                    </div>
                </section>
            </div>

            <section id="affiliate-commissions" class="affiliate-subsection">
                <h2>Lịch sử hoa hồng</h2>
                <div class="table-responsive">
                    <table class="affiliate-table">
                        <thead><tr><th>Đơn hàng</th><th>Số tiền</th><th>Trạng thái</th><th>Ngày</th></tr></thead>
                        <tbody>
                        <?php if (!$commissions) { ?>
                            <tr><td class="affiliate-empty" colspan="4">Chưa có hoa hồng giới thiệu.</td></tr>
                        <?php } else { foreach ($commissions as $commission) { ?>
                            <tr>
                                <td>#<?=(int) $commission['order_id'];?></td>
                                <td><?=number_format((float) $commission['amount'], 0, ',', '.');?>đ</td>
                                <td><?=ecom_h($commissionStatusLabels[$commission['status']] ?? $commission['status']);?></td>
                                <td><?=ecom_h($commission['created_at']);?></td>
                            </tr>
                        <?php }} ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="affiliate-payouts" class="affiliate-subsection">
                <h2>Rút hoa hồng về ngân hàng</h2>
                <p>Yêu cầu sẽ được gửi đến quản trị viên để đối soát và xử lý chuyển khoản.</p>
                <form method="post" class="mb-4">
                    <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                    <input type="hidden" name="action" value="request_payout">
                    <button class="affiliate-request-button" <?=((float) ($availablePayout['total'] ?? 0) <= 0 ? 'disabled' : '');?>>
                        Gửi yêu cầu rút <?=number_format((float) ($availablePayout['total'] ?? 0), 0, ',', '.');?>đ
                    </button>
                </form>
                <div class="table-responsive">
                    <table class="affiliate-table">
                        <thead><tr><th>Mã yêu cầu</th><th>Số tiền</th><th>Trạng thái</th><th>Tham chiếu</th><th>Ngày</th></tr></thead>
                        <tbody>
                        <?php if (!$payouts) { ?>
                            <tr><td class="affiliate-empty" colspan="5">Chưa có yêu cầu rút tiền.</td></tr>
                        <?php } else { foreach ($payouts as $payout) { ?>
                            <tr>
                                <td>#<?=(int) $payout['id'];?></td>
                                <td><?=number_format((float) $payout['amount'], 0, ',', '.');?>đ</td>
                                <td><?=ecom_h($payoutStatusLabels[$payout['status']] ?? $payout['status']);?></td>
                                <td><?=ecom_h($payout['payout_reference']);?></td>
                                <td><?=ecom_h($payout['created_at']);?></td>
                            </tr>
                        <?php }} ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php } ?>
    </div>
</main>
<?php if ($referralUrl !== '') { ?>
<script>
document.getElementById('copy-affiliate-link').addEventListener('click', async function () {
    var input = document.getElementById('affiliate-link');
    try {
        await navigator.clipboard.writeText(input.value);
        document.getElementById('copy-message').textContent = 'Đã sao chép liên kết.';
    } catch (error) {
        input.focus();
        input.select();
        document.getElementById('copy-message').textContent = 'Hãy sao chép liên kết đã chọn.';
    }
});
</script>
<?php } ?>
<?php require_once(__DIR__ . "/Footer.php"); ?>
