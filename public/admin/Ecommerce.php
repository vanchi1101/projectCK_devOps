<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");
require_once(__DIR__ . "/../../includes/login-admin.php");

$title = 'Danh mục sản phẩm | ' . $AZCODE->site('tenweb');
$message = '';
$isInstalled = ecom_is_installed();
$orderStatusLabels = [
    'pending_payment' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán',
    'processing' => 'Đang xử lý',
    'shipped' => 'Đã gửi hàng',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy',
    'refunded' => 'Đã hoàn tiền',
    'payment_review' => 'Chờ đối soát'
];

if ($isInstalled && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create_category') {
            $categoryTitle = trim((string) ($_POST['title'] ?? ''));
            if ($categoryTitle === '' || strlen($categoryTitle) > 190) {
                $message = 'Tên danh mục không hợp lệ.';
            } else {
                $slug = 'cat-' . strtolower(bin2hex(random_bytes(5)));
                $created = $AZCODE->insert('ecom_categories', [
                    'title' => $categoryTitle,
                    'slug' => $slug,
                    'image' => '',
                    'sort_order' => 0,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = $created ? 'Đã tạo danh mục.' : 'Không thể tạo danh mục.';
            }
        } elseif ($action === 'create_product') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $sku = strtoupper(trim((string) ($_POST['sku'] ?? '')));
            $brand = trim((string) ($_POST['brand'] ?? ''));
            $model = trim((string) ($_POST['model'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
            $price = trim((string) ($_POST['price'] ?? ''));
            $priceValid = preg_match('/^\d{1,12}$/D', $price) === 1;
            $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
            $warranty = filter_var($_POST['warranty_months'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 120]]);
            $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $imageValid = $imageUrl === '' || filter_var($imageUrl, FILTER_VALIDATE_URL);
            $imageScheme = $imageUrl === '' || in_array(strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME)), ['http', 'https'], true);
            if ($name === '' || strlen($name) > 190 || strlen($sku) < 3 || strlen($sku) > 80
                || !$priceValid || (float) $price <= 0 || $stock === false || $warranty === false
                || $categoryId === false || !$imageValid || !$imageScheme) {
                $message = 'Thông tin sản phẩm không hợp lệ.';
            } else {
                $categoryId = (int) $categoryId;
                $category = $AZCODE->get_row(
                    "SELECT `id` FROM `ecom_categories` WHERE `id` = '$categoryId' AND `status` = 'active'"
                );
                $created = $category && $AZCODE->insert('ecom_products', [
                    'category_id' => $categoryId,
                    'seller_user_id' => 0,
                    'name' => $name,
                    'sku' => $sku,
                    'brand' => $brand,
                    'model' => $model,
                    'description' => $description,
                    'image_url' => $imageUrl,
                    'price' => number_format((float) $price, 2, '.', ''),
                    'stock' => (int) $stock,
                    'warranty_months' => (int) $warranty,
                    'approval_status' => 'approved',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $message = $created ? 'Đã tạo và mở bán sản phẩm.' : 'Không thể tạo sản phẩm. Hãy kiểm tra danh mục và SKU.';
            }
        } elseif ($action === 'save_settings') {
            $referralRate = filter_var($_POST['referral_rate'] ?? '', FILTER_VALIDATE_FLOAT);
            $sellerRate = filter_var($_POST['seller_rate'] ?? '', FILTER_VALIDATE_FLOAT);
            $shippingFee = filter_var($_POST['shipping_fee'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 999999999]]);
            if ($referralRate === false || $sellerRate === false || $shippingFee === false
                || $referralRate < 0 || $referralRate > 100 || $sellerRate < 0 || $sellerRate > 100 || $shippingFee < 0) {
                $message = 'Mức hoa hồng hoặc phí giao hàng không hợp lệ.';
            } else {
                $saved = true;
                foreach ([
                    'referral_commission_percent' => $referralRate,
                    'seller_fee_percent' => $sellerRate,
                    'shipping_fee' => $shippingFee
                ] as $key => $value) {
                    $keySql = $AZCODE->escape($key);
                    $valueSql = $AZCODE->escape((string) $value);
                    if (!$AZCODE->query(
                        "INSERT INTO `ecom_settings` (`setting_key`,`setting_value`) VALUES ('$keySql','$valueSql')"
                        . " ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)"
                    )) {
                        $saved = false;
                    }
                }
                $message = $saved ? 'Đã lưu cài đặt.' : 'Không thể lưu đầy đủ cài đặt.';
            }
        } elseif ($action === 'review_product') {
            $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $status = (string) ($_POST['approval_status'] ?? '');
            if ($productId === false || !in_array($status, ['approved', 'rejected', 'hidden'], true)) {
                $message = 'Yêu cầu duyệt sản phẩm không hợp lệ.';
            } else {
                $updated = $AZCODE->query(
                    "UPDATE `ecom_products` SET `approval_status` = '" . $AZCODE->escape($status)
                    . "', `updated_at` = '" . date('Y-m-d H:i:s') . "' WHERE `id` = '" . (int) $productId . "'"
                );
                $message = $updated ? 'Đã cập nhật trạng thái sản phẩm.' : 'Không thể cập nhật sản phẩm.';
            }
        } elseif ($action === 'update_order') {
            $orderId = filter_var($_POST['order_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $status = (string) ($_POST['status'] ?? '');
            $carrier = trim((string) ($_POST['shipping_carrier'] ?? ''));
            $trackingNo = trim((string) ($_POST['tracking_no'] ?? ''));
            $refundReference = trim((string) ($_POST['refund_reference'] ?? ''));
            $allowed = ['pending_payment', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded', 'payment_review'];
            if ($orderId === false || !in_array($status, $allowed, true)
                || strlen($carrier) > 120 || strlen($trackingNo) > 120 || strlen($refundReference) > 190) {
                $message = 'Thông tin cập nhật đơn hàng không hợp lệ.';
            } elseif (!$AZCODE->begin_transaction()) {
                $message = 'Không thể khởi tạo cập nhật đơn hàng.';
            } else {
                $orderId = (int) $orderId;
                $order = $AZCODE->get_row("SELECT * FROM `ecom_orders` WHERE `id` = '$orderId' FOR UPDATE");
                if (!$order) {
                    $AZCODE->rollback();
                    $message = 'Không tìm thấy đơn hàng.';
                } elseif ($status === 'cancelled' && $order['payment_status'] !== 'unpaid') {
                    $AZCODE->rollback();
                    $message = 'Chỉ hủy đơn chưa thanh toán; hãy đối soát khoản tiền đã nhận trước.';
                } elseif (!in_array($status, ecom_allowed_order_statuses($order), true)) {
                    $AZCODE->rollback();
                    $message = 'Không thể chuyển đơn hàng sang trạng thái này.';
                } elseif ($status === 'refunded' && $order['status'] !== 'refunded'
                    && (!in_array($order['payment_status'], ['paid', 'mismatch'], true) || $refundReference === '')) {
                    $AZCODE->rollback();
                    $message = 'Chỉ ghi nhận hoàn tiền sau khi đã chuyển tiền hoàn và nhập mã tham chiếu.';
                } else {
                    $stockUpdated = true;
                    if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
                        $stockUpdated = ecom_restore_order_stock($orderId);
                    }
                    if ($status === 'refunded' && $order['status'] === 'payment_review') {
                        $stockUpdated = ecom_restore_order_stock($orderId);
                    }
                    if (!$stockUpdated) {
                        $AZCODE->rollback();
                        $message = 'Không thể cập nhật tồn kho; trạng thái đơn hàng chưa thay đổi.';
                    } else {
                        $carrierSql = $AZCODE->escape($carrier);
                        $trackingSql = $AZCODE->escape($trackingNo);
                        $refundSql = $AZCODE->escape($refundReference);
                        $now = date('Y-m-d H:i:s');
                        $updated = $AZCODE->query(
                            "UPDATE `ecom_orders` SET `status` = '" . $AZCODE->escape($status)
                            . "', `shipping_carrier` = '$carrierSql', `tracking_no` = '$trackingSql',"
                            . " `refund_reference` = '$refundSql'"
                            . ($status === 'completed' && isset($order['payment_method']) && $order['payment_method'] === 'cod'
                                && $order['payment_status'] === 'unpaid'
                                ? ", `payment_status` = 'paid', `paid_at` = '$now'"
                                : '')
                            . ($status === 'refunded' ? ", `payment_status` = 'refunded'" : '')
                            . ", `updated_at` = '$now' WHERE `id` = '$orderId'"
                        );
                        if ($updated && $status === 'completed' && isset($order['payment_method'])
                            && $order['payment_method'] === 'cod' && $order['payment_status'] === 'unpaid') {
                            $updated = ecom_create_commissions($order);
                        }
                        if ($updated && $status === 'cancelled' && $order['status'] !== 'cancelled') {
                            $updated = $AZCODE->query(
                                "UPDATE `ecom_commissions` SET `status` = 'reversed', `updated_at` = '$now'"
                                . " WHERE `order_id` = '$orderId' AND `status` <> 'paid'"
                            );
                        }
                        if ($updated && $status === 'refunded') {
                            $updated = $AZCODE->query(
                                "UPDATE `ecom_commissions` SET `status` = 'reversed', `updated_at` = '$now'"
                                . " WHERE `order_id` = '$orderId' AND `status` <> 'paid'"
                            );
                        }
                        if ($updated && $status === 'completed') {
                            $updated = $AZCODE->query(
                                "UPDATE `ecom_commissions` SET `status` = 'approved', `updated_at` = '$now'"
                                . " WHERE `order_id` = '$orderId' AND `status` = 'pending'"
                            );
                        }
                        if ($updated && $AZCODE->commit()) {
                            $message = 'Đã cập nhật đơn hàng.';
                        } else {
                            $AZCODE->rollback();
                            $message = 'Không thể lưu trạng thái đơn hàng.';
                        }
                    }
                }
            }
        } elseif ($action === 'payout') {
            $payoutId = filter_var($_POST['payout_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $status = (string) ($_POST['status'] ?? '');
            $reference = trim((string) ($_POST['payout_reference'] ?? ''));
            if ($payoutId === false || !in_array($status, ['paid', 'rejected'], true) || strlen($reference) > 190) {
                $message = 'Yêu cầu thanh toán CTV không hợp lệ.';
            } elseif (!$AZCODE->begin_transaction()) {
                $message = 'Không thể khởi tạo đối soát hoa hồng.';
            } else {
                $payoutId = (int) $payoutId;
                $payout = $AZCODE->get_row("SELECT * FROM `ecom_payouts` WHERE `id` = '$payoutId' FOR UPDATE");
                if (!$payout || $payout['status'] !== 'requested') {
                    $AZCODE->rollback();
                    $message = 'Yêu cầu thanh toán không còn ở trạng thái chờ.';
                } else {
                    $now = date('Y-m-d H:i:s');
                    $referenceSql = $AZCODE->escape($reference);
                    $items = [];
                    $validPayout = true;
                    if ($status === 'paid') {
                        $items = $AZCODE->get_list(
                            "SELECT c.`id`,c.`kind`,c.`status`,c.`ctv_user_id` FROM `ecom_payout_items` pi"
                            . " JOIN `ecom_commissions` c ON c.`id` = pi.`commission_id`"
                            . " WHERE pi.`payout_id` = '$payoutId' FOR UPDATE"
                        );
                        $expectedKind = ecom_commission_kind_for_user($payout['ctv_user_id']);
                        $validPayout = !empty($items) && $expectedKind !== null;
                        foreach ($items ?: [] as $item) {
                            if ((int) $item['ctv_user_id'] !== (int) $payout['ctv_user_id']
                                || $item['kind'] !== $expectedKind || $item['status'] !== 'approved') {
                                $validPayout = false;
                                break;
                            }
                        }
                        if (!$validPayout) {
                            $AZCODE->rollback();
                            $message = 'Yêu cầu chứa hoa hồng sai loại hoặc không còn hợp lệ; hãy từ chối và tạo lại yêu cầu.';
                        }
                    }
                    if ($validPayout) {
                        $updated = true;
                        if ($status === 'paid') {
                            foreach ($items as $item) {
                                if (!$AZCODE->query(
                                    "UPDATE `ecom_commissions` SET `status` = 'paid', `updated_at` = '$now'"
                                    . " WHERE `id` = '" . (int) $item['id'] . "' AND `status` = 'approved'"
                                )) {
                                    $updated = false;
                                    break;
                                }
                            }
                        } else {
                            $updated = $AZCODE->query("DELETE FROM `ecom_payout_items` WHERE `payout_id` = '$payoutId'");
                        }
                        if ($updated) {
                            $updated = $AZCODE->query(
                                "UPDATE `ecom_payouts` SET `status` = '" . $AZCODE->escape($status)
                                . "', `payout_reference` = '$referenceSql', `updated_at` = '$now' WHERE `id` = '$payoutId'"
                            );
                        }
                        if ($updated && $AZCODE->commit()) {
                            $message = 'Đã cập nhật yêu cầu thanh toán hoa hồng.';
                        } else {
                            $AZCODE->rollback();
                            $message = 'Không thể lưu yêu cầu thanh toán.';
                        }
                    }
                }
            }
        }
    }
}

$categories = $isInstalled ? $AZCODE->get_list(
    "SELECT * FROM `ecom_categories` ORDER BY `sort_order`,`title`"
) : [];
$perPage = 20;
$productPage = max(1, (int) ($_GET['product_page'] ?? 1));
$orderPage = max(1, (int) ($_GET['order_page'] ?? 1));
$reviewPage = max(1, (int) ($_GET['review_page'] ?? 1));
$payoutPage = max(1, (int) ($_GET['payout_page'] ?? 1));
$products = [];
$orders = [];
$paymentReviews = [];
$payouts = [];
$productTotal = $orderTotal = $reviewTotal = $payoutTotal = 0;
if ($isInstalled) {
    $count = $AZCODE->get_row("SELECT COUNT(*) AS total FROM `ecom_products`");
    $productTotal = (int) ($count['total'] ?? 0);
    $productPage = min($productPage, max(1, (int) ceil($productTotal / $perPage)));
    $offset = ($productPage - 1) * $perPage;
    $products = $AZCODE->get_list(
        "SELECT p.*,u.`username` AS `seller_name`,c.`title` AS `category_title`"
        . " FROM `ecom_products` p LEFT JOIN `users` u ON u.`id` = p.`seller_user_id`"
        . " LEFT JOIN `ecom_categories` c ON c.`id` = p.`category_id`"
        . " ORDER BY FIELD(p.`approval_status`,'pending','approved','rejected','hidden'),p.`created_at` DESC LIMIT $offset,$perPage"
    );

    $count = $AZCODE->get_row("SELECT COUNT(*) AS total FROM `ecom_orders`");
    $orderTotal = (int) ($count['total'] ?? 0);
    $orderPage = min($orderPage, max(1, (int) ceil($orderTotal / $perPage)));
    $offset = ($orderPage - 1) * $perPage;
    $orders = $AZCODE->get_list(
        "SELECT o.*,u.`username` FROM `ecom_orders` o LEFT JOIN `users` u ON u.`id` = o.`customer_user_id`"
        . " ORDER BY o.`created_at` DESC LIMIT $offset,$perPage"
    );

    $count = $AZCODE->get_row("SELECT COUNT(*) AS total FROM `ecom_bank_payments` WHERE `status` <> 'accepted'");
    $reviewTotal = (int) ($count['total'] ?? 0);
    $reviewPage = min($reviewPage, max(1, (int) ceil($reviewTotal / $perPage)));
    $offset = ($reviewPage - 1) * $perPage;
    $paymentReviews = $AZCODE->get_list(
        "SELECT p.*,o.`order_no` FROM `ecom_bank_payments` p"
        . " LEFT JOIN `ecom_orders` o ON o.`id` = p.`order_id`"
        . " WHERE p.`status` <> 'accepted' ORDER BY p.`created_at` DESC LIMIT $offset,$perPage"
    );

    $count = $AZCODE->get_row("SELECT COUNT(*) AS total FROM `ecom_payouts`");
    $payoutTotal = (int) ($count['total'] ?? 0);
    $payoutPage = min($payoutPage, max(1, (int) ceil($payoutTotal / $perPage)));
    $offset = ($payoutPage - 1) * $perPage;
    $payouts = $AZCODE->get_list(
        "SELECT p.*,u.`username` FROM `ecom_payouts` p LEFT JOIN `users` u ON u.`id` = p.`ctv_user_id`"
        . " ORDER BY FIELD(p.`status`,'requested','approved','paid','rejected'),p.`created_at` DESC LIMIT $offset,$perPage"
    );
}

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Sidebar.php");
?>
<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Danh mục sản phẩm</h1></div></section>
    <section class="content"><div class="container-fluid">
        <?php if (!$isInstalled) { ?>
            <div class="alert alert-warning">Sao lưu database, sau đó import <code>ecommerce-migration.sql</code> để khởi tạo các bảng thương mại điện tử.</div>
        <?php } else { ?>
            <?php if ($message !== '') { ?><div class="alert alert-info"><?=ecom_h($message);?></div><?php } ?>
            <div class="card"><div class="card-header"><h3 class="card-title">Cấu hình kinh doanh</h3></div>
                <div class="card-body">
                    <form method="post" class="form-row">
                        <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                        <input type="hidden" name="action" value="save_settings">
                        <div class="form-group col-md-4"><label>Hoa hồng tiếp thị liên kết cho người dùng (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="referral_rate" value="<?=ecom_h(ecom_setting('referral_commission_percent','5'));?>" required></div>
                        <div class="form-group col-md-4"><label>Phí nền tảng trừ trên đơn CTV (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="seller_rate" value="<?=ecom_h(ecom_setting('seller_fee_percent','5'));?>" required></div>
                        <div class="form-group col-md-4"><label>Phí giao hàng mặc định (VND)</label><input class="form-control" type="number" step="1" min="0" name="shipping_fee" value="<?=ecom_h(ecom_setting('shipping_fee','0'));?>" required></div>
                        <div class="col-12"><button class="btn btn-primary">Lưu cấu hình</button></div>
                    </form>
                </div>
            </div>
            <div class="card"><div class="card-header"><h3 class="card-title">Danh mục</h3></div>
                <div class="card-body">
                    <form method="post" class="form-inline mb-3">
                        <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                        <input type="hidden" name="action" value="create_category">
                        <input class="form-control mr-2" name="title" maxlength="190" placeholder="Tên danh mục điện tử" required>
                        <button class="btn btn-primary">Thêm danh mục</button>
                    </form>
                    <?php foreach ($categories as $category) { ?><span class="badge badge-info mr-1"><?=ecom_h($category['title']);?></span><?php } ?>
                </div>
            </div>
            <div class="card"><div class="card-header"><h3 class="card-title">Thêm sản phẩm của cửa hàng</h3></div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                        <input type="hidden" name="action" value="create_product">
                        <div class="form-row">
                            <div class="form-group col-md-4"><label>Tên sản phẩm</label><input class="form-control" name="name" maxlength="190" required></div>
                            <div class="form-group col-md-2"><label>SKU</label><input class="form-control" name="sku" maxlength="80" required></div>
                            <div class="form-group col-md-3"><label>Danh mục</label><select class="form-control" name="category_id" required><option value="">Chọn</option><?php foreach ($categories as $category) { ?><option value="<?=(int) $category['id'];?>"><?=ecom_h($category['title']);?></option><?php } ?></select></div>
                            <div class="form-group col-md-3"><label>Giá (VND)</label><input class="form-control" type="number" step="1" min="1" name="price" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3"><label>Thương hiệu</label><input class="form-control" name="brand" maxlength="120"></div>
                            <div class="form-group col-md-3"><label>Model</label><input class="form-control" name="model" maxlength="120"></div>
                            <div class="form-group col-md-2"><label>Tồn kho</label><input class="form-control" type="number" min="0" name="stock" value="0" required></div>
                            <div class="form-group col-md-2"><label>Bảo hành (tháng)</label><input class="form-control" type="number" min="0" max="120" name="warranty_months" value="12"></div>
                            <div class="form-group col-md-2"><label>URL ảnh</label><input class="form-control" type="url" name="image_url" maxlength="500"></div>
                        </div>
                        <div class="form-group"><label>Mô tả/thông số</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                        <button class="btn btn-success">Tạo và mở bán</button>
                    </form>
                </div>
            </div>
            <div class="card"><div class="card-header"><h3 class="card-title">Duyệt sản phẩm CTV</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered"><thead><tr><th>Sản phẩm</th><th>SKU</th><th>CTV bán</th><th>Giá</th><th>Tồn</th><th>Trạng thái</th><th>Duyệt</th></tr></thead><tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr><td><?=ecom_h($product['name']);?></td><td><?=ecom_h($product['sku']);?></td><td><?=ecom_h($product['seller_name']);?></td><td><?=number_format((float) $product['price'],0,',','.');?> đ</td><td><?=(int) $product['stock'];?></td><td><?=ecom_h($product['approval_status']);?></td><td>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="action" value="review_product"><input type="hidden" name="product_id" value="<?=(int) $product['id'];?>">
                                <select class="form-control form-control-sm mr-1" name="approval_status">
                                    <option value="approved">Duyệt</option><option value="rejected">Từ chối</option><option value="hidden">Ẩn</option>
                                </select><button class="btn btn-sm btn-primary">Lưu</button>
                            </form>
                        </td></tr>
                    <?php } ?>
                    </tbody></table>
                    <?=pagination_control(BASE_URL('Admin/Ecommerce'), $productPage, $productTotal, $perPage, 'product_page');?>
                </div>
            </div>
            <?php if (!ecom_supports_cash_on_delivery()) { ?>
                <div class="alert alert-warning">Để bật thanh toán khi nhận hàng, hãy sao lưu database và chạy một lần file <code>ecommerce-cod-migration.sql</code>.</div>
            <?php } ?>
            <div class="card"><div class="card-header"><h3 class="card-title">Đơn hàng và giao vận</h3></div>
                <div class="card-body table-responsive">
                    <p class="text-muted">Với đơn COD, cập nhật trạng thái thành “Hoàn tất” để xác nhận đã thu tiền khi giao hàng.</p>
                    <table class="table table-bordered"><thead><tr><th>Đơn</th><th>Khách hàng</th><th>Địa chỉ</th><th>Tổng</th><th>Thanh toán</th><th>Trạng thái / vận đơn</th><th>Cập nhật</th></tr></thead><tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr><td><?=ecom_h($order['order_no']);?><br><small><?=ecom_h($order['created_at']);?></small></td>
                            <td><?=ecom_h($order['username']);?><br><?=ecom_h($order['customer_name']);?><br><?=ecom_h($order['customer_phone']);?></td>
                            <td><?=ecom_h($order['shipping_address']);?><br><?=ecom_h($order['shipping_city']);?></td>
                            <td><?=number_format((float) $order['total'],0,',','.');?> đ</td><td><?=isset($order['payment_method']) && $order['payment_method'] === 'cod' ? 'COD - ' : 'Chuyển khoản - ';?><?=ecom_h($order['payment_status']);?></td>
                            <td><?=ecom_h($order['status']);?><br><?=ecom_h($order['shipping_carrier']);?> <?=ecom_h($order['tracking_no']);?><br><?=ecom_h($order['refund_reference']);?></td>
                            <td><form method="post">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="action" value="update_order"><input type="hidden" name="order_id" value="<?=(int) $order['id'];?>">
                                <select class="form-control form-control-sm mb-1" name="status">
                                    <?php foreach (ecom_allowed_order_statuses($order) as $orderStatus) { ?>
                                        <option value="<?=ecom_h($orderStatus);?>"<?=$orderStatus === $order['status'] ? ' selected' : '';?>><?=ecom_h($orderStatusLabels[$orderStatus] ?? $orderStatus);?></option>
                                    <?php } ?>
                                </select>
                                <input class="form-control form-control-sm mb-1" name="shipping_carrier" maxlength="120" placeholder="Đơn vị vận chuyển" value="<?=ecom_h($order['shipping_carrier']);?>">
                                <input class="form-control form-control-sm mb-1" name="tracking_no" maxlength="120" placeholder="Mã vận đơn" value="<?=ecom_h($order['tracking_no']);?>">
                                <input class="form-control form-control-sm mb-1" name="refund_reference" maxlength="190" placeholder="Mã tham chiếu hoàn tiền" value="<?=ecom_h($order['refund_reference']);?>">
                                <button class="btn btn-sm btn-primary">Cập nhật</button>
                            </form></td>
                        </tr>
                    <?php } ?>
                    </tbody></table>
                    <?=pagination_control(BASE_URL('Admin/Ecommerce'), $orderPage, $orderTotal, $perPage, 'order_page');?>
                </div>
            </div>
            <div class="card"><div class="card-header"><h3 class="card-title">Giao dịch cần đối soát</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered"><thead><tr><th>Đơn</th><th>Mã giao dịch</th><th>Số tiền</th><th>Trạng thái</th><th>Nội dung chuyển khoản</th><th>Thời gian</th></tr></thead><tbody>
                    <?php foreach ($paymentReviews as $payment) { ?>
                        <tr><td><?=ecom_h($payment['order_no']);?></td><td><?=ecom_h($payment['transaction_id']);?></td><td><?=number_format((float) $payment['amount'],0,',','.');?> đ</td><td><?=ecom_h($payment['status']);?></td><td><?=ecom_h($payment['description']);?></td><td><?=ecom_h($payment['created_at']);?></td></tr>
                    <?php } ?>
                    </tbody></table>
                    <?=pagination_control(BASE_URL('Admin/Ecommerce'), $reviewPage, $reviewTotal, $perPage, 'review_page');?>
                </div>
            </div>
            <div class="card"><div class="card-header"><h3 class="card-title">Đối soát thanh toán hoa hồng</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered"><thead><tr><th>Người nhận</th><th>Số tiền</th><th>Trạng thái</th><th>Tham chiếu chuyển khoản</th><th>Thao tác</th></tr></thead><tbody>
                    <?php foreach ($payouts as $payout) { ?>
                        <tr><td><?=ecom_h($payout['username']);?></td><td><?=number_format((float) $payout['amount'],0,',','.');?> đ</td><td><?=ecom_h($payout['status']);?></td><td><?=ecom_h($payout['payout_reference']);?></td><td>
                            <?php if ($payout['status'] === 'requested') { ?>
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                    <input type="hidden" name="action" value="payout"><input type="hidden" name="payout_id" value="<?=(int) $payout['id'];?>">
                                    <input class="form-control form-control-sm mr-1" name="payout_reference" maxlength="190" placeholder="Mã giao dịch / ghi chú">
                                    <button class="btn btn-sm btn-success mr-1" name="status" value="paid">Đã thanh toán</button>
                                    <button class="btn btn-sm btn-secondary" name="status" value="rejected">Từ chối</button>
                                </form>
                            <?php } ?>
                        </td></tr>
                    <?php } ?>
                    </tbody></table>
                    <?=pagination_control(BASE_URL('Admin/Ecommerce'), $payoutPage, $payoutTotal, $perPage, 'payout_page');?>
                </div>
            </div>
        <?php } ?>
    </div></section>
</div>
<?php require_once(__DIR__ . "/Footer.php"); ?>
