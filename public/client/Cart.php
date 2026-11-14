<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

$title = 'GIỎ HÀNG | ' . $AZCODE->site('tenweb');
$message = '';
$isInstalled = ecom_is_installed();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!ecom_check_csrf()) {
        $message = 'Phiên biểu mẫu không hợp lệ, vui lòng tải lại trang.';
    } elseif (!$isInstalled) {
        $message = 'Cửa hàng điện tử chưa được khởi tạo.';
    } else {
        $id = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $action = (string) ($_POST['action'] ?? '');
        $cart = ecom_cart();
        if ($action === 'remove' && $id !== false) {
            unset($cart[(int) $id]);
            $_SESSION['ecom_cart'] = $cart;
            header('Location: ' . BASE_URL('Cart'));
            exit();
        } elseif (in_array($action, ['update', 'increment', 'decrement'], true) && $id !== false) {
            $productId = (int) $id;
            if (!isset($cart[$productId])) {
                $message = 'Sản phẩm không còn trong giỏ hàng.';
            } else {
                $product = $AZCODE->get_row(
                    "SELECT `stock`,`approval_status` FROM `ecom_products` WHERE `id` = '$productId' LIMIT 1"
                );
                if (!$product || $product['approval_status'] !== 'approved') {
                    $message = 'Sản phẩm không còn được bán. Vui lòng xóa khỏi giỏ hàng.';
                } elseif ($action === 'update') {
                    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
                    if ($quantity === false) {
                        $message = 'Số lượng không hợp lệ.';
                    } elseif ((int) $quantity > (int) $product['stock']) {
                        $message = 'Số lượng vượt quá tồn kho hiện tại.';
                    } else {
                        $cart[$productId] = (int) $quantity;
                    }
                } elseif ($action === 'increment') {
                    $maximum = min(99, (int) $product['stock']);
                    if ((int) $cart[$productId] >= $maximum) {
                        $message = $maximum > 0
                            ? 'Đã đạt số lượng tồn kho tối đa.'
                            : 'Sản phẩm hiện đã hết hàng.';
                    } else {
                        $cart[$productId] = (int) $cart[$productId] + 1;
                    }
                } elseif ((int) $cart[$productId] <= 1) {
                    $message = 'Số lượng tối thiểu là 1. Dùng nút Xóa để bỏ sản phẩm.';
                } else {
                    $cart[$productId] = (int) $cart[$productId] - 1;
                }

                if ($message === '') {
                    $_SESSION['ecom_cart'] = $cart;
                    header('Location: ' . BASE_URL('Cart'));
                    exit();
                }
            }
        }
    }
}
$items = $isInstalled ? ecom_cart_items() : [];
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['line_total'];
}
require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<style>
    .cart-page { min-height: 72vh; padding: clamp(24px, 5vw, 52px) 16px 64px; color: #eaf2fb; }
    .cart-container { width: min(1050px, 100%); margin: 0 auto; }
    .cart-heading { margin: 0 0 7px; color: #fff; font-size: clamp(1.8rem, 4vw, 2.4rem); font-weight: 850; }
    .cart-subheading { margin: 0 0 24px; color: #b7c8dc; }
    .cart-alert {
        margin-bottom: 16px;
        padding: 13px 16px;
        border: 1px solid rgba(252,165,165,.5);
        border-radius: 12px;
        background: rgba(127,29,29,.72);
        color: #fff1f2;
    }
    .cart-empty, .cart-item, .cart-summary {
        border: 1px solid rgba(226,232,240,.72);
        border-radius: 18px;
        background: rgba(255,255,255,.97);
        color: #172b46;
        box-shadow: 0 16px 38px rgba(0,0,0,.14);
    }
    .cart-empty { padding: 38px 20px; text-align: center; }
    .cart-empty strong { display: block; margin-bottom: 8px; font-size: 1.15rem; }
    .cart-empty a { display: inline-block; margin-top: 15px; color: #1768a6; font-weight: 800; text-decoration: none; }
    .cart-items { display: grid; gap: 13px; }
    .cart-item { display: grid; grid-template-columns: 94px minmax(0,1fr) auto; gap: 17px; align-items: center; padding: 16px; }
    .cart-item-image { width: 94px; height: 94px; padding: 7px; border-radius: 12px; background: #edf4fa; object-fit: contain; }
    .cart-item-placeholder { display: grid; width: 94px; height: 94px; place-items: center; border-radius: 12px; background: #edf4fa; color: #718198; font-size: .75rem; text-align: center; }
    .cart-item-name { color: #142640; font-size: 1.02rem; font-weight: 800; text-decoration: none; }
    .cart-item-name:hover { color: #1677d2; }
    .cart-item-unit { margin: 6px 0 0; color: #718198; font-size: .9rem; }
    .cart-item-total { margin-top: 7px; color: #1263a5; font-size: 1rem; font-weight: 850; }
    .cart-item-actions { display: flex; flex-direction: column; align-items: end; gap: 11px; }
    .cart-quantity-control { display: flex; align-items: center; gap: 6px; }
    .cart-quantity-control input {
        width: 62px;
        height: 40px;
        padding: 0 7px;
        border: 1px solid #cbd9e8;
        border-radius: 9px;
        color: #172b46;
        background: #fff;
        text-align: center;
        font-weight: 750;
    }
    .cart-quantity-button, .cart-update-button {
        display: inline-grid;
        min-width: 40px;
        height: 40px;
        place-items: center;
        border: 1px solid #cbd9e8;
        border-radius: 9px;
        background: #f4f8fc;
        color: #214766;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
    }
    .cart-quantity-button:hover:not(:disabled) { border-color: #80b4df; background: #e8f4fd; }
    .cart-quantity-button:disabled { opacity: .42; cursor: not-allowed; }
    .cart-update-button { min-width: auto; padding: 0 12px; background: #173d70; color: #fff; font-size: .85rem; }
    .cart-remove-button { padding: 0; border: 0; background: transparent; color: #b42318; font-weight: 750; cursor: pointer; }
    .cart-stock-note { max-width: 245px; margin: 0; color: #b45309; font-size: .78rem; text-align: right; }
    .cart-summary { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-top: 18px; padding: 20px 22px; }
    .cart-summary-label { margin: 0; color: #64748b; font-size: .9rem; }
    .cart-summary-total { margin: 4px 0 0; color: #142640; font-size: 1.35rem; font-weight: 850; }
    .cart-checkout-button {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        border-radius: 11px;
        background: linear-gradient(115deg, #1677d2, #0d8c9b);
        color: #fff;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(22,119,210,.2);
    }
    @media (max-width: 680px) {
        .cart-page { padding: 24px 12px 48px; }
        .cart-item { grid-template-columns: 70px minmax(0,1fr); gap: 12px; padding: 12px; }
        .cart-item-image, .cart-item-placeholder { width: 70px; height: 70px; }
        .cart-item-actions { grid-column: 1 / -1; flex-direction: row; align-items: center; justify-content: space-between; width: 100%; }
        .cart-stock-note { text-align: left; }
        .cart-summary { align-items: stretch; flex-direction: column; padding: 17px; }
        .cart-checkout-button { width: 100%; }
    }
</style>
<main class="cart-page">
    <div class="cart-container">
        <h1 class="cart-heading">Giỏ hàng</h1>
        <p class="cart-subheading">Kiểm tra sản phẩm và số lượng trước khi đặt hàng.</p>
        <?php if ($message !== '') { ?><div class="cart-alert" role="alert"><?=ecom_h($message);?></div><?php } ?>
        <?php if (!$items) { ?>
            <div class="cart-empty">
                <strong>Giỏ hàng đang trống</strong>
                Sản phẩm có thể đã hết hàng hoặc không còn được bán.
                <a href="<?=ecom_h(BASE_URL('Shop'));?>">Tiếp tục mua sắm</a>
            </div>
        <?php } else { ?>
            <div class="cart-items">
                <?php foreach ($items as $item) { ?>
                    <?php $maximum = min(99, (int) $item['stock']); ?>
                    <article class="cart-item">
                        <?php if ($item['image_url']) { ?>
                            <img src="<?=ecom_h($item['image_url']);?>" alt="<?=ecom_h($item['name']);?>" class="cart-item-image">
                        <?php } else { ?>
                            <div class="cart-item-placeholder">Chưa có ảnh</div>
                        <?php } ?>
                        <div>
                            <a class="cart-item-name" href="<?=ecom_h(BASE_URL('Product/' . (int) $item['id']));?>"><?=ecom_h($item['name']);?></a>
                            <p class="cart-item-unit"><?=number_format((float) $item['price'], 0, ',', '.');?> đ / sản phẩm</p>
                            <p class="cart-item-total"><?=number_format((float) $item['line_total'], 0, ',', '.');?> đ</p>
                            <?php if ((int) $item['quantity'] > (int) $item['stock']) { ?>
                                <p class="cart-stock-note">Số lượng trong giỏ đang vượt tồn kho còn lại.</p>
                            <?php } ?>
                        </div>
                        <div class="cart-item-actions">
                            <form method="post" class="cart-quantity-control">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="product_id" value="<?=(int) $item['id'];?>">
                                <button class="cart-quantity-button" type="submit" name="action" value="decrement"
                                        aria-label="Giảm số lượng" <?=((int) $item['quantity'] <= 1) ? 'disabled' : '';?>>−</button>
                                <input name="quantity" type="number" min="1" max="<?=max(1, $maximum);?>" value="<?=(int) $item['quantity'];?>"
                                       aria-label="Số lượng sản phẩm">
                                <button class="cart-quantity-button" type="submit" name="action" value="increment"
                                        aria-label="Tăng số lượng" <?=((int) $item['quantity'] >= $maximum) ? 'disabled' : '';?>>+</button>
                                <button class="cart-update-button" type="submit" name="action" value="update">Cập nhật</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="product_id" value="<?=(int) $item['id'];?>">
                                <button class="cart-remove-button" type="submit" name="action" value="remove">Xóa sản phẩm</button>
                            </form>
                        </div>
                    </article>
                <?php } ?>
            </div>
            <div class="cart-summary">
                <div>
                    <p class="cart-summary-label">Tạm tính (chưa gồm phí giao hàng)</p>
                    <p class="cart-summary-total"><?=number_format($subtotal, 0, ',', '.');?> đ</p>
                </div>
                <a class="cart-checkout-button" href="<?=ecom_h(BASE_URL('Checkout'));?>">Tiến hành đặt hàng</a>
            </div>
        <?php } ?>
    </div>
</main>
<?php require_once(__DIR__ . "/Footer.php"); ?>
