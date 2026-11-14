<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");
require_once(__DIR__ . "/../../includes/login-ctv-any.php");

$title = 'KÊNH BÁN HÀNG CTV | ' . $AZCODE->site('tenweb');
$message = '';
$isInstalled = ecom_is_installed();

if ($isInstalled && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } elseif (($_POST['action'] ?? '') === 'request_payout') {
        $payout = ecom_request_payout((int) $getUser['id'], 'seller');
        $message = isset($payout['error'])
            ? $payout['error']
            : 'Đã gửi yêu cầu thanh toán hoa hồng #' . (int) $payout['payout_id'] . '.';
    } elseif (($_POST['action'] ?? '') === 'create_product') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $sku = strtoupper(trim((string) ($_POST['sku'] ?? '')));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $model = trim((string) ($_POST['model'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
        $priceInput = trim((string) ($_POST['price'] ?? ''));
        $priceValid = preg_match('/^\d{1,12}$/D', $priceInput) === 1;
        $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
        $warranty = filter_var($_POST['warranty_months'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 120]]);
        $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $imageValid = $imageUrl === '' || filter_var($imageUrl, FILTER_VALIDATE_URL);
        $imageScheme = $imageUrl === '' ? true : in_array(strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME)), ['http', 'https'], true);

        if ($name === '' || strlen($name) > 190 || strlen($sku) < 3 || strlen($sku) > 80
            || !$priceValid || (float) $priceInput <= 0 || $stock === false || $warranty === false
            || $categoryId === false || !$imageValid || !$imageScheme) {
            $message = 'Thông tin sản phẩm không hợp lệ. Hãy kiểm tra tên, SKU, giá, tồn kho, danh mục và đường dẫn ảnh.';
        } else {
            $categoryId = (int) $categoryId;
            $category = $AZCODE->get_row(
                "SELECT `id` FROM `ecom_categories` WHERE `id` = '$categoryId' AND `status` = 'active'"
            );
            if (!$category) {
                $message = 'Danh mục sản phẩm không tồn tại.';
            } else {
                $created = $AZCODE->insert('ecom_products', [
                    'category_id' => $categoryId,
                    'seller_user_id' => (int) $getUser['id'],
                    'name' => $name,
                    'sku' => $sku,
                    'brand' => $brand,
                    'model' => $model,
                    'description' => $description,
                    'image_url' => $imageUrl,
                    'price' => number_format((float) $priceInput, 2, '.', ''),
                    'stock' => (int) $stock,
                    'warranty_months' => (int) $warranty,
                    'approval_status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $message = $created ? 'Sản phẩm đã gửi admin duyệt.' : 'Không thể tạo sản phẩm. SKU có thể đã được sử dụng.';
            }
        }
    } elseif (($_POST['action'] ?? '') === 'update_product') {
        $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
        $priceInput = trim((string) ($_POST['price'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $brand = trim((string) ($_POST['brand'] ?? ''));
        $model = trim((string) ($_POST['model'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
        $priceValid = preg_match('/^\d{1,12}$/D', $priceInput) === 1;
        $imageValid = $imageUrl === '' || filter_var($imageUrl, FILTER_VALIDATE_URL);
        $imageScheme = $imageUrl === '' || in_array(strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME)), ['http', 'https'], true);
        if ($productId === false || $stock === false || !$priceValid || (float) $priceInput <= 0
            || $name === '' || strlen($name) > 190 || strlen($brand) > 120 || strlen($model) > 120
            || strlen($imageUrl) > 500 || !$imageValid || !$imageScheme) {
            $message = 'Thông tin sản phẩm không hợp lệ.';
        } else {
            $productId = (int) $productId;
            $updated = $AZCODE->query(
                "UPDATE `ecom_products` SET `name` = '" . $AZCODE->escape($name)
                . "', `brand` = '" . $AZCODE->escape($brand) . "', `model` = '" . $AZCODE->escape($model)
                . "', `description` = '" . $AZCODE->escape($description) . "', `image_url` = '" . $AZCODE->escape($imageUrl)
                . "', `price` = '" . $AZCODE->escape(number_format((float) $priceInput, 2, '.', ''))
                . "', `stock` = '" . (int) $stock . "', `approval_status` = 'pending', `updated_at` = '"
                . date('Y-m-d H:i:s') . "' WHERE `id` = '$productId' AND `seller_user_id` = '" . (int) $getUser['id'] . "'"
            );
            $message = $updated ? 'Đã cập nhật sản phẩm; sản phẩm được chuyển về trạng thái chờ duyệt.' : 'Không thể cập nhật sản phẩm.';
        }
    }
}

$categories = $isInstalled ? $AZCODE->get_list(
    "SELECT `id`,`title` FROM `ecom_categories` WHERE `status` = 'active' ORDER BY `sort_order`,`title`"
) : [];
$products = $isInstalled ? $AZCODE->get_list(
    "SELECT p.*, c.`title` AS `category_title` FROM `ecom_products` p"
    . " LEFT JOIN `ecom_categories` c ON c.`id` = p.`category_id`"
    . " WHERE p.`seller_user_id` = '" . (int) $getUser['id'] . "' ORDER BY p.`created_at` DESC"
) : [];
$commissions = $isInstalled ? $AZCODE->get_list(
    "SELECT * FROM `ecom_commissions` WHERE `ctv_user_id` = '" . (int) $getUser['id'] . "'"
    . " AND `kind` = 'seller' ORDER BY `created_at` DESC LIMIT 100"
) : [];
$availablePayout = $isInstalled ? $AZCODE->get_row(
    "SELECT COALESCE(SUM(c.`amount`),0) AS `total` FROM `ecom_commissions` c"
    . " LEFT JOIN `ecom_payout_items` pi ON pi.`commission_id` = c.`id`"
    . " WHERE c.`ctv_user_id` = '" . (int) $getUser['id'] . "' AND c.`status` = 'approved'"
    . " AND c.`kind` = 'seller'"
    . " AND pi.`commission_id` IS NULL"
) : false;
$payouts = $isInstalled ? $AZCODE->get_list(
    "SELECT DISTINCT p.* FROM `ecom_payouts` p"
    . " JOIN `ecom_payout_items` pi ON pi.`payout_id` = p.`id`"
    . " JOIN `ecom_commissions` c ON c.`id` = pi.`commission_id`"
    . " WHERE p.`ctv_user_id` = '" . (int) $getUser['id'] . "' AND c.`kind` = 'seller'"
    . " ORDER BY p.`created_at` DESC LIMIT 50"
) : [];
$sellerOrders = $isInstalled ? $AZCODE->get_list(
    "SELECT o.`id`,o.`order_no`,o.`status`,o.`payment_status`,o.`created_at`,"
    . " GROUP_CONCAT(CONCAT(i.`product_name`,' × ',i.`quantity`) SEPARATOR ', ') AS `items`"
    . " FROM `ecom_orders` o JOIN `ecom_order_items` i ON i.`order_id` = o.`id`"
    . " WHERE i.`seller_user_id` = '" . (int) $getUser['id'] . "'"
    . " GROUP BY o.`id` ORDER BY o.`created_at` DESC LIMIT 100"
) : [];

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Sidebar.php");
?>
<div class="content-wrapper">
    <section class="content-header"><div class="container-fluid"><h1>Kênh bán hàng CTV</h1></div></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (!$isInstalled) { ?>
                <div class="alert alert-warning">Cần import <code>ecommerce-migration.sql</code> trước khi sử dụng.</div>
            <?php } else { ?>
                <?php if ($message !== '') { ?><div class="alert alert-info"><?=ecom_h($message);?></div><?php } ?>
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Đăng sản phẩm điện tử</h3></div>
                    <div class="card-body">
                        <p>Sản phẩm mới hoặc thay đổi thông tin sẽ chờ admin duyệt trước khi hiển thị công khai.</p>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                            <input type="hidden" name="action" value="create_product">
                            <div class="form-group"><label>Tên sản phẩm</label><input class="form-control" name="name" maxlength="190" required></div>
                            <div class="form-row">
                                <div class="form-group col-md-4"><label>SKU</label><input class="form-control" name="sku" maxlength="80" required></div>
                                <div class="form-group col-md-4"><label>Danh mục</label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php foreach ($categories as $category) { ?><option value="<?=(int) $category['id'];?>"><?=ecom_h($category['title']);?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4"><label>Giá (VND)</label><input class="form-control" type="number" min="1" step="1" name="price" required></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4"><label>Thương hiệu</label><input class="form-control" name="brand" maxlength="120"></div>
                                <div class="form-group col-md-4"><label>Model</label><input class="form-control" name="model" maxlength="120"></div>
                                <div class="form-group col-md-2"><label>Tồn kho</label><input class="form-control" type="number" min="0" name="stock" value="0" required></div>
                                <div class="form-group col-md-2"><label>Bảo hành (tháng)</label><input class="form-control" type="number" min="0" max="120" name="warranty_months" value="12"></div>
                            </div>
                            <div class="form-group"><label>URL ảnh (http/https)</label><input class="form-control" type="url" name="image_url" maxlength="500"></div>
                            <div class="form-group"><label>Mô tả/thông số</label><textarea class="form-control" name="description" rows="4"></textarea></div>
                            <button class="btn btn-primary">Gửi duyệt</button>
                        </form>
                    </div>
                </div>
                <div class="card"><div class="card-header"><h3 class="card-title">Sản phẩm của tôi</h3></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered"><thead><tr><th>Sản phẩm</th><th>SKU</th><th>Trạng thái</th><th>Chỉnh sửa</th></tr></thead><tbody>
                        <?php foreach ($products as $product) { ?>
                            <tr>
                                <td><?=ecom_h($product['name']);?><br><small><?=ecom_h($product['brand']);?> <?=ecom_h($product['model']);?></small></td>
                                <td><?=ecom_h($product['sku']);?></td><td><?=ecom_h($product['approval_status']);?></td>
                                <td><form method="post">
                                    <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                    <input type="hidden" name="action" value="update_product"><input type="hidden" name="product_id" value="<?=(int) $product['id'];?>">
                                    <div class="form-row">
                                        <div class="form-group col-md-4"><label>Tên</label><input class="form-control form-control-sm" name="name" maxlength="190" value="<?=ecom_h($product['name']);?>" required></div>
                                        <div class="form-group col-md-2"><label>Thương hiệu</label><input class="form-control form-control-sm" name="brand" maxlength="120" value="<?=ecom_h($product['brand']);?>"></div>
                                        <div class="form-group col-md-2"><label>Model</label><input class="form-control form-control-sm" name="model" maxlength="120" value="<?=ecom_h($product['model']);?>"></div>
                                        <div class="form-group col-md-2"><label>Giá</label><input class="form-control form-control-sm" type="number" min="1" step="1" name="price" value="<?=ecom_h($product['price']);?>" required></div>
                                        <div class="form-group col-md-2"><label>Tồn kho</label><input class="form-control form-control-sm" type="number" min="0" name="stock" value="<?=(int) $product['stock'];?>" required></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4"><label>URL ảnh</label><input class="form-control form-control-sm" type="url" name="image_url" maxlength="500" value="<?=ecom_h($product['image_url']);?>"></div>
                                        <div class="form-group col-md-8"><label>Mô tả/thông số</label><textarea class="form-control form-control-sm" name="description" rows="2"><?=ecom_h($product['description']);?></textarea></div>
                                    </div>
                                    <button class="btn btn-sm btn-primary">Lưu và gửi duyệt</button>
                                </form></td>
                            </tr>
                        <?php } ?>
                        </tbody></table>
                    </div>
                </div>
                <div class="card"><div class="card-header"><h3 class="card-title">Đơn hàng có sản phẩm của tôi</h3></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered"><thead><tr><th>Đơn</th><th>Sản phẩm</th><th>Thanh toán</th><th>Trạng thái</th><th>Ngày</th></tr></thead><tbody>
                        <?php foreach ($sellerOrders as $order) { ?>
                            <tr><td><?=ecom_h($order['order_no']);?></td><td><?=ecom_h($order['items']);?></td><td><?=ecom_h($order['payment_status']);?></td><td><?=ecom_h($order['status']);?></td><td><?=ecom_h($order['created_at']);?></td></tr>
                        <?php } ?>
                        </tbody></table>
                    </div>
                </div>
                <div class="card"><div class="card-header"><h3 class="card-title">Doanh thu bán hàng</h3></div>
                    <div class="card-body">
                        <p>Thu nhập được tính từ các đơn hàng có sản phẩm của bạn sau khi trừ phí nền tảng. Số dư chỉ được duyệt sau khi đơn hoàn tất.</p>
                        <p>Được duyệt và có thể yêu cầu thanh toán: <strong><?=number_format((float) ($availablePayout['total'] ?? 0), 0, ',', '.');?> đ</strong></p>
                        <form method="post" class="mb-3">
                            <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                            <input type="hidden" name="action" value="request_payout">
                            <button class="btn btn-success" <?=((float) ($availablePayout['total'] ?? 0) <= 0 ? 'disabled' : '');?>>Yêu cầu thanh toán doanh thu đã duyệt</button>
                        </form>
                        <div class="table-responsive"><table class="table table-bordered">
                            <thead><tr><th>Đơn</th><th>Thu nhập bán hàng</th><th>Trạng thái</th><th>Ngày</th></tr></thead><tbody>
                            <?php foreach ($commissions as $commission) { ?>
                                <tr><td>#<?=(int) $commission['order_id'];?></td><td><?=number_format((float) $commission['amount'], 0, ',', '.');?> đ</td><td><?=ecom_h($commission['status']);?></td><td><?=ecom_h($commission['created_at']);?></td></tr>
                            <?php } ?>
                            </tbody>
                        </table></div>
                        <h4>Yêu cầu thanh toán</h4>
                        <div class="table-responsive"><table class="table table-bordered">
                            <thead><tr><th>Mã</th><th>Số tiền</th><th>Trạng thái</th><th>Tham chiếu</th></tr></thead><tbody>
                            <?php foreach ($payouts as $payout) { ?>
                                <tr><td>#<?=(int) $payout['id'];?></td><td><?=number_format((float) $payout['amount'], 0, ',', '.');?> đ</td><td><?=ecom_h($payout['status']);?></td><td><?=ecom_h($payout['payout_reference']);?></td></tr>
                            <?php } ?>
                            </tbody>
                        </table></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
</div>
<?php require_once(__DIR__ . "/Footer.php"); ?>
