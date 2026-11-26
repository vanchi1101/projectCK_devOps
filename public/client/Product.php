<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

ecom_capture_referrer();
$isInstalled = ecom_is_installed();
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$product = !$isInstalled || $id === false ? false : $AZCODE->get_row(
    "SELECT p.*, c.`title` AS `category_title` FROM `ecom_products` p"
    . " LEFT JOIN `ecom_categories` c ON c.`id` = p.`category_id`"
    . " WHERE p.`id` = '" . (int) $id . "' AND p.`approval_status` = 'approved'"
);
$productDescription = $product ? trim((string) $product['description']) : '';
if ($productDescription !== '') {
    $productDescription = preg_replace('/\s*Nguồn:\s*https?:\/\/\S+.*$/u', '', $productDescription);
}
$title = $product ? ecom_h($product['name']) . ' | ' . $AZCODE->site('tenweb') : 'Sản phẩm không tồn tại';
$message = '';

if ($product && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } else {
        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
        if ($quantity === false) {
            $message = 'Số lượng không hợp lệ.';
        } elseif ((int) $product['stock'] < $quantity) {
            $message = 'Sản phẩm không đủ tồn kho.';
        } else {
            $cart = ecom_cart();
            $cart[(int) $product['id']] = min(99, (int) ($cart[(int) $product['id']] ?? 0) + $quantity);
            $_SESSION['ecom_cart'] = $cart;
            header('Location: ' . BASE_URL('Cart'));
            exit();
        }
    }
}

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<style>
    .product-detail-page {
        min-height: 75vh;
        padding: clamp(24px, 5vw, 60px) 16px;
        color: #172b46;
        background:
            radial-gradient(ellipse at 10% 12%, rgba(147,197,253,.34), transparent 34%),
            radial-gradient(ellipse at 92% 18%, rgba(165,243,252,.35), transparent 30%),
            linear-gradient(180deg, #f8fbff 0%, #edf4fb 100%);
    }
    .product-detail-shell { width: min(1120px, 100%); margin: 0 auto; }
    .product-detail-card {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
        gap: clamp(24px, 5vw, 58px);
        padding: clamp(20px, 4vw, 42px);
        border: 1px solid #e0e8f1;
        border-radius: 24px;
        background: rgba(255,255,255,.95);
        box-shadow: 0 22px 54px rgba(15,42,75,.1);
    }
    .product-detail-visual {
        display: grid;
        min-height: 360px;
        place-items: center;
        overflow: hidden;
        padding: 24px;
        border: 1px solid #e2eaf3;
        border-radius: 18px;
        background: linear-gradient(145deg, #f1f7fc, #e8f1fa);
    }
    .product-detail-visual img { width: 100%; max-height: 460px; object-fit: contain; }
    .product-detail-placeholder {
        display: grid;
        width: 100%;
        min-height: 300px;
        place-items: center;
        border: 1px dashed #b9cde0;
        border-radius: 14px;
        color: #66809c;
        font-weight: 700;
    }
    .product-detail-info { align-self: center; }
    .product-detail-kicker { margin: 0 0 10px; color: #1677a8; font-size: .78rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .product-detail-info h1 { margin: 0; color: #142640; font-size: clamp(1.8rem, 4vw, 2.65rem); font-weight: 850; line-height: 1.14; }
    .product-detail-sku { margin: 12px 0 0; color: #718198; font-size: .9rem; }
    .product-detail-brand { margin: 5px 0 0; color: #52657e; }
    .product-detail-price { margin: 22px 0 0; color: #1263a5; font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 850; }
    .product-detail-facts { display: flex; flex-wrap: wrap; gap: 9px; margin: 18px 0; }
    .product-detail-facts span { padding: 8px 11px; border: 1px solid #d7e6f4; border-radius: 999px; background: #f4f9fd; color: #355674; font-size: .83rem; font-weight: 700; }
    .product-detail-description { margin: 20px 0; color: #52657e; line-height: 1.75; }
    .product-detail-form { display: flex; flex-wrap: wrap; align-items: end; gap: 12px; margin-top: 24px; }
    .product-detail-form label { display: grid; gap: 7px; color: #52657e; font-size: .85rem; font-weight: 700; }
    .product-detail-form input {
        width: 92px;
        min-height: 46px;
        padding: 0 12px;
        border: 1px solid #cbd9e8;
        border-radius: 10px;
        color: #172b46;
        background: #fff;
    }
    .product-detail-form button {
        min-height: 46px;
        padding: 0 20px;
        border: 0;
        border-radius: 10px;
        color: #fff;
        background: linear-gradient(115deg, #1677d2, #0d8c9b);
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 9px 20px rgba(22,119,210,.2);
    }
    .product-detail-error, .product-detail-unavailable {
        padding: 18px;
        border: 1px solid #e0e8f1;
        border-radius: 15px;
        background: #fff;
        color: #52657e;
        text-align: center;
    }
    .product-detail-error { margin-bottom: 18px; border-color: #fecaca; background: #fff7f7; color: #b42318; }
    .product-detail-out { margin: 18px 0 0; color: #b42318; font-weight: 750; }
    @media (max-width: 760px) {
        .product-detail-page { padding: 22px 12px 42px; }
        .product-detail-card { grid-template-columns: 1fr; gap: 24px; padding: 16px; border-radius: 19px; }
        .product-detail-visual { min-height: 260px; }
        .product-detail-visual img { max-height: 330px; }
    }
</style>
<main class="product-detail-page">
    <div class="product-detail-shell">
    <?php if (!$isInstalled) { ?>
        <p class="product-detail-unavailable">Cửa hàng điện tử chưa được khởi tạo. Vui lòng liên hệ quản trị viên.</p>
    <?php } elseif (!$product) { ?>
        <p class="product-detail-unavailable">Sản phẩm không tồn tại hoặc chưa được duyệt.</p>
    <?php } else { ?>
        <?php if ($message !== '') { ?><div class="product-detail-error"><?=ecom_h($message);?></div><?php } ?>
        <div class="product-detail-card">
            <div class="product-detail-visual">
                <?php if (!empty($product['image_url'])) { ?>
                    <img src="<?=ecom_h($product['image_url']);?>" alt="<?=ecom_h($product['name']);?>"
                         loading="lazy">
                <?php } else { ?>
                    <div class="product-detail-placeholder">Hình ảnh sản phẩm</div>
                <?php } ?>
            </div>
            <section class="product-detail-info">
                <p class="product-detail-kicker"><?=ecom_h($product['category_title'] ?? 'Công nghệ');?></p>
                <h1><?=ecom_h($product['name']);?></h1>
                <p class="product-detail-sku">Mã sản phẩm: <?=ecom_h($product['sku']);?></p>
                <p class="product-detail-brand"><?=ecom_h(trim($product['brand'] . ' ' . $product['model']));?></p>
                <p class="product-detail-price"><?=number_format((float) $product['price'], 0, ',', '.');?> đ</p>
                <div class="product-detail-facts">
                    <span>Còn <?=number_format((int) $product['stock']);?> sản phẩm</span>
                    <?php if ((int) $product['warranty_months'] > 0) { ?>
                        <span>Bảo hành <?=number_format((int) $product['warranty_months']);?> tháng</span>
                    <?php } ?>
                </div>
                <?php if ($productDescription !== '') { ?>
                    <div class="product-detail-description"><?=nl2br(ecom_h($productDescription));?></div>
                <?php } ?>
                <?php if ((int) $product['stock'] > 0) { ?>
                    <form method="post" class="product-detail-form">
                        <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                        <label for="quantity">Số lượng
                        <input id="quantity" name="quantity" type="number" min="1" max="<?=min(99, (int) $product['stock']);?>" value="1"
                               >
                        </label>
                        <button type="submit">Thêm vào giỏ hàng</button>
                    </form>
                <?php } else { ?>
                    <p class="product-detail-out">Sản phẩm đã hết hàng.</p>
                <?php } ?>
            </section>
        </div>
    <?php } ?>
    </div>
</main>
<?php require_once(__DIR__ . "/Footer.php"); ?>
