<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");
require_once(__DIR__ . "/../../includes/login-admin.php");

$title = 'Danh mục sản phẩm | ' . $AZCODE->site('tenweb');
$message = '';
$isInstalled = ecom_is_installed();
$restoreScrollY = filter_var($_POST['scroll_y'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 0, 'max_range' => 10000000]
]);
$restoreScrollY = $restoreScrollY === false ? 0 : (int) $restoreScrollY;
$textLength = static function ($value) {
    $matches = [];
    $length = preg_match_all('/./us', (string) $value, $matches);
    return $length === false ? PHP_INT_MAX : $length;
};
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
            if ($categoryTitle === '' || $textLength($categoryTitle) > 190) {
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
        } elseif ($action === 'update_category') {
            $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $categoryTitle = trim((string) ($_POST['title'] ?? ''));
            if ($categoryId === false || $categoryTitle === '' || $textLength($categoryTitle) > 190) {
                $message = 'Tên danh mục không hợp lệ.';
            } else {
                $categoryId = (int) $categoryId;
                $category = $AZCODE->get_row(
                    "SELECT `id` FROM `ecom_categories` WHERE `id` = '$categoryId'"
                );
                if (!$category) {
                    $message = 'Không tìm thấy danh mục cần cập nhật.';
                } else {
                    $updated = $AZCODE->query(
                        "UPDATE `ecom_categories` SET `title` = '" . $AZCODE->escape($categoryTitle)
                        . "' WHERE `id` = '$categoryId'"
                    );
                    $message = $updated ? 'Đã cập nhật danh mục.' : 'Không thể cập nhật danh mục.';
                }
            }
        } elseif ($action === 'delete_category') {
            $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($categoryId === false) {
                $message = 'Yêu cầu xóa danh mục không hợp lệ.';
            } elseif (!$AZCODE->begin_transaction()) {
                $message = 'Không thể khởi tạo thao tác xóa danh mục.';
            } else {
                $categoryId = (int) $categoryId;
                $category = $AZCODE->get_row(
                    "SELECT `id` FROM `ecom_categories` WHERE `id` = '$categoryId' FOR UPDATE"
                );
                if (!$category) {
                    $AZCODE->rollback();
                    $message = 'Không tìm thấy danh mục cần xóa.';
                } else {
                    $productsUnassigned = $AZCODE->query(
                        "UPDATE `ecom_products` SET `category_id` = NULL WHERE `category_id` = '$categoryId'"
                    );
                    $deleted = $productsUnassigned && $AZCODE->query(
                        "DELETE FROM `ecom_categories` WHERE `id` = '$categoryId'"
                    );
                    if ($deleted && $AZCODE->commit()) {
                        $message = 'Đã xóa danh mục. Sản phẩm thuộc danh mục này được chuyển sang “Chưa phân loại”.';
                    } else {
                        $AZCODE->rollback();
                        $message = 'Không thể xóa danh mục.';
                    }
                }
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
        } elseif ($action === 'update_product') {
            $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $name = trim((string) ($_POST['name'] ?? ''));
            $sku = strtoupper(trim((string) ($_POST['sku'] ?? '')));
            $brand = trim((string) ($_POST['brand'] ?? ''));
            $model = trim((string) ($_POST['model'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $imageUrl = trim((string) ($_POST['image_url'] ?? ''));
            $price = trim((string) ($_POST['price'] ?? ''));
            $priceValid = preg_match('/^\d{1,12}$/D', $price) === 1;
            $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
            $warranty = filter_var($_POST['warranty_months'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 120]]);
            $categoryId = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $imageValid = $imageUrl === '' || filter_var($imageUrl, FILTER_VALIDATE_URL);
            $imageScheme = $imageUrl === '' || in_array(strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME)), ['http', 'https'], true);

            if ($productId === false || $name === '' || strlen($name) > 190 || strlen($sku) < 3 || strlen($sku) > 80
                || strlen($brand) > 120 || strlen($model) > 120 || strlen($imageUrl) > 500
                || !$priceValid || (float) $price <= 0 || $stock === false || $warranty === false
                || $categoryId === false || !$imageValid || !$imageScheme) {
                $message = 'Thông tin sản phẩm không hợp lệ.';
            } else {
                $categoryId = (int) $categoryId;
                $category = $AZCODE->get_row(
                    "SELECT `id` FROM `ecom_categories` WHERE `id` = '$categoryId' AND `status` = 'active'"
                );
                if (!$category) {
                    $message = 'Danh mục sản phẩm không tồn tại hoặc đã bị ẩn.';
                } else {
                    $updated = $AZCODE->query(
                        "UPDATE `ecom_products` SET `category_id` = '$categoryId', `name` = '" . $AZCODE->escape($name)
                        . "', `sku` = '" . $AZCODE->escape($sku) . "', `brand` = '" . $AZCODE->escape($brand)
                        . "', `model` = '" . $AZCODE->escape($model) . "', `description` = '" . $AZCODE->escape($description)
                        . "', `image_url` = '" . $AZCODE->escape($imageUrl)
                        . "', `price` = '" . $AZCODE->escape(number_format((float) $price, 2, '.', ''))
                        . "', `stock` = '" . (int) $stock . "', `warranty_months` = '" . (int) $warranty
                        . "', `updated_at` = '" . date('Y-m-d H:i:s') . "' WHERE `id` = '" . (int) $productId . "'"
                    );
                    $message = $updated
                        ? 'Đã cập nhật thông tin và số lượng sản phẩm.'
                        : 'Không thể cập nhật sản phẩm. SKU có thể đã được sử dụng.';
                }
            }
        } elseif ($action === 'delete_product') {
            $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($productId === false) {
                $message = 'Yêu cầu xóa sản phẩm không hợp lệ.';
            } elseif (!$AZCODE->begin_transaction()) {
                $message = 'Không thể khởi tạo thao tác xóa sản phẩm.';
            } else {
                $productId = (int) $productId;
                $product = $AZCODE->get_row(
                    "SELECT `id` FROM `ecom_products` WHERE `id` = '$productId' FOR UPDATE"
                );
                if (!$product) {
                    $AZCODE->rollback();
                    $message = 'Không tìm thấy sản phẩm cần xóa.';
                } else {
                    $orderItem = $AZCODE->get_row(
                        "SELECT `id` FROM `ecom_order_items` WHERE `product_id` = '$productId' LIMIT 1 FOR UPDATE"
                    );
                    if ($orderItem) {
                        $AZCODE->rollback();
                        $message = 'Sản phẩm đã có lịch sử đơn hàng nên không thể xóa. Hãy chuyển trạng thái sang “Đã ẩn”.';
                    } else {
                        $deleted = $AZCODE->query(
                            "DELETE FROM `ecom_products` WHERE `id` = '$productId'"
                        );
                        if ($deleted && $AZCODE->commit()) {
                            $message = 'Đã xóa sản phẩm.';
                        } else {
                            $AZCODE->rollback();
                            $message = 'Không thể xóa sản phẩm.';
                        }
                    }
                }
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
            if ($productId === false || !in_array($status, ['pending', 'approved', 'rejected', 'hidden'], true)) {
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
$adminPaginationParams = [
    'product_page' => $productPage,
    'order_page' => $orderPage,
    'review_page' => $reviewPage,
    'payout_page' => $payoutPage
];
$adminPaginationUrl = BASE_URL('Admin/Ecommerce') . '?' . http_build_query($adminPaginationParams + ['tab' => 'products']);
$adminOrdersPaginationUrl = BASE_URL('Admin/Ecommerce') . '?' . http_build_query($adminPaginationParams + ['tab' => 'orders']);
$adminPaymentsPaginationUrl = BASE_URL('Admin/Ecommerce') . '?' . http_build_query($adminPaginationParams + ['tab' => 'payments']);
$adminTabs = ['settings', 'categories', 'products', 'orders', 'payments'];
$activeTab = (string) ($_POST['active_tab'] ?? $_GET['tab'] ?? 'products');
if (!in_array($activeTab, $adminTabs, true)) {
    $activeTab = 'products';
}
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
    <section class="content-header">
        <div class="container-fluid">
            <h1>Quản lý cửa hàng</h1>
            <p class="text-muted mb-0">Quản lý sản phẩm, đơn hàng và thanh toán tại từng mục riêng.</p>
        </div>
    </section>
    <section class="content ecommerce-admin">
        <div class="container-fluid">
        <?php if (!$isInstalled) { ?>
            <div class="alert alert-warning">Sao lưu database, sau đó import <code>ecommerce-migration.sql</code> để khởi tạo các bảng thương mại điện tử.</div>
        <?php } else { ?>
            <?php if ($message !== '') { ?><div class="alert alert-info"><?=ecom_h($message);?></div><?php } ?>
            <style>
                .ecommerce-admin .nav-pills { gap: .45rem; margin-bottom: 1.25rem; }
                .ecommerce-admin .nav-pills .nav-link { background: #fff; border: 1px solid #dfe5ec; color: #526174; border-radius: .55rem; font-weight: 600; }
                .ecommerce-admin .nav-pills .nav-link.active { background: #0878e8; border-color: #0878e8; color: #fff; box-shadow: 0 4px 12px rgba(0, 123, 255, .18); }
                .ecommerce-admin .card { border: 1px solid #e3e8ef; border-radius: .65rem; box-shadow: 0 2px 8px rgba(28, 39, 54, .04); }
                .ecommerce-admin .card-header { background: #fff; border-bottom-color: #edf0f4; padding: 1rem 1.25rem; }
                .ecommerce-admin .card-title { font-size: 1.05rem; font-weight: 600; }
                .ecommerce-admin .form-group label { font-weight: 600; color: #39485a; }
                .ecommerce-admin .table th { white-space: nowrap; border-top: 0; }
                .ecommerce-admin .table td { vertical-align: middle; }
                .ecommerce-admin .product-edit-row > td { background: #f7f9fc; padding: 1.25rem; }
                .ecommerce-admin .category-chip { display: inline-flex; align-items: center; padding: .5rem .75rem; margin: .25rem; background: #eef6ff; color: #1768b2; border-radius: 2rem; font-weight: 600; }
                .ecommerce-admin .category-management-list { display: grid; gap: .65rem; max-width: 760px; }
                .ecommerce-admin .category-management-item { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .65rem; padding: .65rem; border: 1px solid #e8edf3; border-radius: .6rem; }
                .ecommerce-admin .category-management-item .category-chip { margin: 0; }
                .ecommerce-admin .category-management-edit { width: 100%; padding: .5rem .25rem .1rem; }
                @media (max-width: 575.98px) { .ecommerce-admin .nav-pills .nav-link { padding: .5rem .65rem; font-size: .9rem; } }
            </style>
            <ul class="nav nav-pills flex-wrap" role="tablist">
                <li class="nav-item"><a class="nav-link<?=$activeTab === 'products' ? ' active' : '';?>" data-toggle="pill" data-tab-key="products" href="#shop-products" role="tab"><i class="fas fa-box-open mr-1"></i>Sản phẩm</a></li>
                <li class="nav-item"><a class="nav-link<?=$activeTab === 'categories' ? ' active' : '';?>" data-toggle="pill" data-tab-key="categories" href="#shop-categories" role="tab"><i class="fas fa-tags mr-1"></i>Danh mục</a></li>
                <li class="nav-item"><a class="nav-link<?=$activeTab === 'orders' ? ' active' : '';?>" data-toggle="pill" data-tab-key="orders" href="#shop-orders" role="tab"><i class="fas fa-truck mr-1"></i>Đơn hàng</a></li>
                <li class="nav-item"><a class="nav-link<?=$activeTab === 'payments' ? ' active' : '';?>" data-toggle="pill" data-tab-key="payments" href="#shop-payments" role="tab"><i class="fas fa-wallet mr-1"></i>Thanh toán</a></li>
                <li class="nav-item"><a class="nav-link<?=$activeTab === 'settings' ? ' active' : '';?>" data-toggle="pill" data-tab-key="settings" href="#shop-settings" role="tab"><i class="fas fa-sliders-h mr-1"></i>Cấu hình</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade<?=$activeTab === 'products' ? ' show active' : '';?>" id="shop-products" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                            <div><h3 class="card-title mb-1">Sản phẩm cửa hàng</h3><div class="text-muted small"><?=$productTotal;?> sản phẩm</div></div>
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#add-store-product" aria-expanded="false"><i class="fas fa-plus mr-1"></i>Thêm sản phẩm</button>
                        </div>
                        <div class="collapse" id="add-store-product">
                            <div class="card-body border-bottom">
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                    <input type="hidden" name="action" value="create_product">
                                    <div class="form-row">
                                        <div class="form-group col-lg-4 col-md-6"><label>Tên sản phẩm</label><input class="form-control" name="name" maxlength="190" required></div>
                                        <div class="form-group col-lg-2 col-md-6"><label>SKU</label><input class="form-control" name="sku" maxlength="80" required></div>
                                        <div class="form-group col-lg-3 col-md-6"><label>Danh mục</label><select class="form-control" name="category_id" required><option value="">Chọn danh mục</option><?php foreach ($categories as $category) { if ($category['status'] === 'active') { ?><option value="<?=(int) $category['id'];?>"><?=ecom_h($category['title']);?></option><?php } } ?></select></div>
                                        <div class="form-group col-lg-3 col-md-6"><label>Giá bán (VND)</label><input class="form-control" type="number" step="1" min="1" name="price" required></div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3"><label>Thương hiệu</label><input class="form-control" name="brand" maxlength="120"></div>
                                        <div class="form-group col-md-3"><label>Model</label><input class="form-control" name="model" maxlength="120"></div>
                                        <div class="form-group col-md-2"><label>Số lượng tồn</label><input class="form-control" type="number" min="0" max="1000000" name="stock" value="0" required></div>
                                        <div class="form-group col-md-2"><label>Bảo hành (tháng)</label><input class="form-control" type="number" min="0" max="120" name="warranty_months" value="12" required></div>
                                        <div class="form-group col-md-2"><label>URL ảnh</label><input class="form-control" type="url" name="image_url" maxlength="500"></div>
                                    </div>
                                    <div class="form-group"><label>Mô tả / thông số</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                                    <button class="btn btn-success"><i class="fas fa-check mr-1"></i>Tạo và mở bán</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th>Sản phẩm</th><th>Danh mục</th><th>SKU</th><th>Giá</th><th>Tồn kho</th><th>Người bán</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                                <tbody>
                                <?php if (empty($products)) { ?><tr><td colspan="8" class="text-center text-muted py-4">Chưa có sản phẩm nào.</td></tr><?php } ?>
                                <?php foreach ($products as $product) { $productId = (int) $product['id']; ?>
                                    <tr>
                                        <td><strong><?=ecom_h($product['name']);?></strong><br><small class="text-muted"><?=ecom_h(trim((string) $product['brand'] . ' ' . (string) $product['model']));?></small></td>
                                        <td><?=ecom_h($product['category_title'] ?: 'Chưa phân loại');?></td>
                                        <td><?=ecom_h($product['sku']);?></td>
                                        <td><?=number_format((float) $product['price'],0,',','.');?> đ</td>
                                        <td><span class="badge <?=$product['stock'] > 0 ? 'badge-success' : 'badge-secondary';?>"><?=(int) $product['stock'];?></span></td>
                                        <td><?=ecom_h($product['seller_name'] ?: 'Cửa hàng');?></td>
                                        <td><?=ecom_h($product['approval_status']);?></td>
                                        <td class="text-nowrap">
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#edit-product-<?=$productId;?>" aria-expanded="false"><i class="fas fa-pen mr-1"></i>Sửa</button>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                                <input type="hidden" name="action" value="review_product"><input type="hidden" name="product_id" value="<?=$productId;?>">
                                                <select class="form-control form-control-sm d-inline-block" style="width:auto" name="approval_status" aria-label="Trạng thái sản phẩm">
                                                    <?php foreach (['pending' => 'Chờ duyệt', 'approved' => 'Đang bán', 'rejected' => 'Từ chối', 'hidden' => 'Đã ẩn'] as $statusValue => $statusLabel) { ?>
                                                        <option value="<?=ecom_h($statusValue);?>"<?=$statusValue === $product['approval_status'] ? ' selected' : '';?>><?=ecom_h($statusLabel);?></option>
                                                    <?php } ?>
                                                </select>
                                                <button class="btn btn-sm btn-outline-secondary" title="Lưu trạng thái"><i class="fas fa-save"></i></button>
                                            </form>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                                <input type="hidden" name="action" value="delete_product"><input type="hidden" name="product_id" value="<?=$productId;?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Xóa sản phẩm" aria-label="Xóa sản phẩm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr class="collapse product-edit-row" id="edit-product-<?=$productId;?>">
                                        <td colspan="8">
                                            <form method="post">
                                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                                <input type="hidden" name="action" value="update_product"><input type="hidden" name="product_id" value="<?=$productId;?>">
                                                <div class="form-row">
                                                    <div class="form-group col-lg-4 col-md-6"><label>Tên sản phẩm</label><input class="form-control" name="name" maxlength="190" value="<?=ecom_h($product['name']);?>" required></div>
                                                    <div class="form-group col-lg-2 col-md-6"><label>SKU</label><input class="form-control" name="sku" maxlength="80" value="<?=ecom_h($product['sku']);?>" required></div>
                                                    <div class="form-group col-lg-3 col-md-6"><label>Danh mục</label><select class="form-control" name="category_id" required><?php foreach ($categories as $category) { if ($category['status'] === 'active' || (int) $category['id'] === (int) $product['category_id']) { ?><option value="<?=(int) $category['id'];?>"<?=(int) $category['id'] === (int) $product['category_id'] ? ' selected' : '';?>><?=ecom_h($category['title']);?></option><?php } } ?></select></div>
                                                    <div class="form-group col-lg-3 col-md-6"><label>Giá bán (VND)</label><input class="form-control" type="number" step="1" min="1" name="price" value="<?=(int) $product['price'];?>" required></div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-3"><label>Thương hiệu</label><input class="form-control" name="brand" maxlength="120" value="<?=ecom_h($product['brand']);?>"></div>
                                                    <div class="form-group col-md-3"><label>Model</label><input class="form-control" name="model" maxlength="120" value="<?=ecom_h($product['model']);?>"></div>
                                                    <div class="form-group col-md-2"><label>Số lượng tồn</label><input class="form-control" type="number" min="0" max="1000000" name="stock" value="<?=(int) $product['stock'];?>" required></div>
                                                    <div class="form-group col-md-2"><label>Bảo hành (tháng)</label><input class="form-control" type="number" min="0" max="120" name="warranty_months" value="<?=(int) $product['warranty_months'];?>" required></div>
                                                    <div class="form-group col-md-2"><label>URL ảnh</label><input class="form-control" type="url" name="image_url" maxlength="500" value="<?=ecom_h($product['image_url']);?>"></div>
                                                </div>
                                                <div class="form-group"><label>Mô tả / thông số</label><textarea class="form-control" name="description" rows="3"><?=ecom_h($product['description']);?></textarea></div>
                                                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Lưu thay đổi</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <?=pagination_control($adminPaginationUrl, $productPage, $productTotal, $perPage, 'product_page');?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade<?=$activeTab === 'categories' ? ' show active' : '';?>" id="shop-categories" role="tabpanel">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Danh mục sản phẩm</h3></div>
                        <div class="card-body">
                            <p class="text-muted">Tạo danh mục để phân loại sản phẩm trong cửa hàng.</p>
                            <form method="post" class="form-row align-items-end mb-4">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="action" value="create_category">
                                <div class="form-group col-md-6 mb-md-0"><label>Tên danh mục</label><input class="form-control" name="title" maxlength="190" placeholder="Ví dụ: Laptop, Phụ kiện" required></div>
                                <div class="form-group col-md-3 mb-md-0"><button class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Thêm danh mục</button></div>
                            </form>
                            <h5 class="mb-2">Danh mục hiện có <span class="badge badge-light"><?=count($categories);?></span></h5>
                            <?php if (empty($categories)) { ?><p class="text-muted mb-0">Chưa có danh mục nào.</p><?php } ?>
                            <div class="category-management-list">
                                <?php foreach ($categories as $category) { $categoryId = (int) $category['id']; ?>
                                    <div class="category-management-item">
                                        <span class="category-chip"><i class="fas fa-tag mr-2"></i><?=ecom_h($category['title']);?><?php if ($category['status'] !== 'active') { ?><small class="ml-2 text-muted">(ẩn)</small><?php } ?></span>
                                        <div class="text-nowrap">
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#edit-category-<?=$categoryId;?>" aria-expanded="false"><i class="fas fa-pen mr-1"></i>Sửa</button>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Xóa danh mục này? Sản phẩm thuộc danh mục sẽ được chuyển sang Chưa phân loại.');">
                                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                                <input type="hidden" name="action" value="delete_category"><input type="hidden" name="category_id" value="<?=$categoryId;?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Xóa danh mục" aria-label="Xóa danh mục"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                        <form method="post" class="collapse category-management-edit" id="edit-category-<?=$categoryId;?>">
                                            <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                            <input type="hidden" name="action" value="update_category"><input type="hidden" name="category_id" value="<?=$categoryId;?>">
                                            <div class="form-row align-items-end">
                                                <div class="form-group col-md-8 mb-md-0"><label for="category-title-<?=$categoryId;?>">Tên danh mục</label><input id="category-title-<?=$categoryId;?>" class="form-control" name="title" maxlength="190" value="<?=ecom_h($category['title']);?>" required></div>
                                                <div class="form-group col-md-4 mb-md-0"><button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Lưu tên</button></div>
                                            </div>
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade<?=$activeTab === 'orders' ? ' show active' : '';?>" id="shop-orders" role="tabpanel">
                    <?php if (!ecom_supports_cash_on_delivery()) { ?>
                        <div class="alert alert-warning">Để bật thanh toán khi nhận hàng, hãy sao lưu database và chạy một lần file <code>ecommerce-cod-migration.sql</code>.</div>
                    <?php } ?>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Đơn hàng và giao vận <small class="text-muted">(<?=$orderTotal;?>)</small></h3></div>
                        <div class="card-body">
                            <p class="text-muted">Với đơn COD, cập nhật trạng thái thành “Hoàn tất” để xác nhận đã thu tiền khi giao hàng.</p>
                            <div class="table-responsive"><table class="table table-hover">
                                <thead><tr><th>Đơn hàng</th><th>Khách hàng</th><th>Địa chỉ</th><th>Tổng</th><th>Thanh toán</th><th>Trạng thái / vận đơn</th><th>Cập nhật</th></tr></thead><tbody>
                                <?php if (empty($orders)) { ?><tr><td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng.</td></tr><?php } ?>
                                <?php foreach ($orders as $order) { ?>
                                    <tr><td><?=ecom_h($order['order_no']);?><br><small><?=ecom_h($order['created_at']);?></small></td>
                                        <td><?=ecom_h($order['username']);?><br><?=ecom_h($order['customer_name']);?><br><?=ecom_h($order['customer_phone']);?></td>
                                        <td><?=ecom_h($order['shipping_address']);?><br><?=ecom_h($order['shipping_city']);?></td>
                                        <td><?=number_format((float) $order['total'],0,',','.');?> đ</td><td><?=isset($order['payment_method']) && $order['payment_method'] === 'cod' ? 'COD - ' : 'Chuyển khoản - ';?><?=ecom_h($order['payment_status']);?></td>
                                        <td><?=ecom_h($orderStatusLabels[$order['status']] ?? $order['status']);?><br><?=ecom_h($order['shipping_carrier']);?> <?=ecom_h($order['tracking_no']);?><br><?=ecom_h($order['refund_reference']);?></td>
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
                                </tbody>
                            </table></div>
                            <?=pagination_control($adminOrdersPaginationUrl, $orderPage, $orderTotal, $perPage, 'order_page');?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade<?=$activeTab === 'payments' ? ' show active' : '';?>" id="shop-payments" role="tabpanel">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Giao dịch cần đối soát <small class="text-muted">(<?=$reviewTotal;?>)</small></h3></div>
                        <div class="card-body"><div class="table-responsive"><table class="table table-hover">
                            <thead><tr><th>Đơn</th><th>Mã giao dịch</th><th>Số tiền</th><th>Trạng thái</th><th>Nội dung chuyển khoản</th><th>Thời gian</th></tr></thead><tbody>
                            <?php if (empty($paymentReviews)) { ?><tr><td colspan="6" class="text-center text-muted py-4">Không có giao dịch cần đối soát.</td></tr><?php } ?>
                            <?php foreach ($paymentReviews as $payment) { ?>
                                <tr><td><?=ecom_h($payment['order_no']);?></td><td><?=ecom_h($payment['transaction_id']);?></td><td><?=number_format((float) $payment['amount'],0,',','.');?> đ</td><td><?=ecom_h($payment['status']);?></td><td><?=ecom_h($payment['description']);?></td><td><?=ecom_h($payment['created_at']);?></td></tr>
                            <?php } ?>
                            </tbody></table></div>
                            <?=pagination_control($adminPaymentsPaginationUrl, $reviewPage, $reviewTotal, $perPage, 'review_page');?>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Đối soát hoa hồng CTV <small class="text-muted">(<?=$payoutTotal;?>)</small></h3></div>
                        <div class="card-body"><div class="table-responsive"><table class="table table-hover">
                            <thead><tr><th>Người nhận</th><th>Số tiền</th><th>Trạng thái</th><th>Tham chiếu chuyển khoản</th><th>Thao tác</th></tr></thead><tbody>
                            <?php if (empty($payouts)) { ?><tr><td colspan="5" class="text-center text-muted py-4">Chưa có yêu cầu thanh toán.</td></tr><?php } ?>
                            <?php foreach ($payouts as $payout) { ?>
                                <tr><td><?=ecom_h($payout['username']);?></td><td><?=number_format((float) $payout['amount'],0,',','.');?> đ</td><td><?=ecom_h($payout['status']);?></td><td><?=ecom_h($payout['payout_reference']);?></td><td>
                                    <?php if ($payout['status'] === 'requested') { ?>
                                        <form method="post" class="form-inline">
                                            <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                            <input type="hidden" name="action" value="payout"><input type="hidden" name="payout_id" value="<?=(int) $payout['id'];?>">
                                            <input class="form-control form-control-sm mr-1 mb-1" name="payout_reference" maxlength="190" placeholder="Mã giao dịch / ghi chú">
                                            <button class="btn btn-sm btn-success mr-1 mb-1" name="status" value="paid">Đã thanh toán</button>
                                            <button class="btn btn-sm btn-secondary mb-1" name="status" value="rejected">Từ chối</button>
                                        </form>
                                    <?php } ?>
                                </td></tr>
                            <?php } ?>
                            </tbody></table></div>
                            <?=pagination_control($adminPaymentsPaginationUrl, $payoutPage, $payoutTotal, $perPage, 'payout_page');?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade<?=$activeTab === 'settings' ? ' show active' : '';?>" id="shop-settings" role="tabpanel">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title mb-0">Cấu hình kinh doanh</h3></div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?=ecom_h(ecom_csrf_token());?>">
                                <input type="hidden" name="action" value="save_settings">
                                <div class="form-row">
                                    <div class="form-group col-lg-4"><label>Hoa hồng tiếp thị liên kết (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="referral_rate" value="<?=ecom_h(ecom_setting('referral_commission_percent','5'));?>" required><small class="form-text text-muted">Tỷ lệ hoa hồng cho người giới thiệu khách hàng.</small></div>
                                    <div class="form-group col-lg-4"><label>Phí nền tảng trên đơn CTV (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="seller_rate" value="<?=ecom_h(ecom_setting('seller_fee_percent','5'));?>" required><small class="form-text text-muted">Phí nền tảng khấu trừ từ đơn do CTV bán.</small></div>
                                    <div class="form-group col-lg-4"><label>Phí giao hàng mặc định (VND)</label><input class="form-control" type="number" step="1" min="0" name="shipping_fee" value="<?=ecom_h(ecom_setting('shipping_fee','0'));?>" required><small class="form-text text-muted">Phí áp dụng mặc định cho mỗi đơn hàng.</small></div>
                                </div>
                                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Lưu cấu hình</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                (function () {
                    var requestPending = false;

                    var showRequestError = function (message) {
                        var container = document.querySelector('.content-wrapper .content .container-fluid');
                        if (!container) return;
                        var alert = document.createElement('div');
                        alert.className = 'alert alert-danger';
                        alert.setAttribute('role', 'alert');
                        alert.textContent = message;
                        container.insertBefore(alert, container.firstChild);
                    };

                    var loadAdminPage = async function (url, method, body, historyMode, activeTab) {
                        if (requestPending) return;
                        requestPending = true;
                        var wrapper = document.querySelector('.content-wrapper');
                        if (!wrapper) {
                            requestPending = false;
                            return;
                        }
                        var scrollY = window.scrollY || window.pageYOffset || 0;
                        var openCollapses = Array.prototype.map.call(
                            wrapper.querySelectorAll('.ecommerce-admin .collapse.show'),
                            function (element) { return element.id; }
                        ).filter(Boolean);
                        wrapper.setAttribute('aria-busy', 'true');

                        try {
                            var options = {
                                method: method,
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            };
                            if (body) options.body = body;
                            var response = await fetch(url.toString(), options);
                            var contentType = response.headers.get('Content-Type') || '';
                            if (contentType.toLowerCase().indexOf('json') !== -1) {
                                var payload = await response.json();
                                var responseMessage = payload && typeof payload.message === 'string'
                                    ? payload.message
                                    : payload && typeof payload.error === 'string'
                                        ? payload.error
                                        : response.ok
                                            ? 'The server reported that the request failed.'
                                            : 'Admin request failed: ' + response.status;
                                if (!response.ok || (payload && payload.success === false)) {
                                    throw new Error(responseMessage);
                                }
                                throw new Error('Admin request returned JSON instead of an HTML page.');
                            }
                            if (!response.ok) throw new Error('Admin request failed: ' + response.status);

                            var responseHtml = await response.text();
                            var parsedDocument = new DOMParser().parseFromString(responseHtml, 'text/html');
                            var updatedWrapper = parsedDocument.querySelector('.content-wrapper');
                            if (!updatedWrapper) throw new Error('Admin content was not found in the response.');

                            wrapper.innerHTML = updatedWrapper.innerHTML;
                            wrapper.setAttribute('aria-busy', 'false');
                            openCollapses.forEach(function (id) {
                                var collapse = document.getElementById(id);
                                if (collapse) {
                                    collapse.classList.add('show');
                                    var toggle = document.querySelector('[data-target="#' + id + '"]');
                                    if (toggle) toggle.setAttribute('aria-expanded', 'true');
                                }
                            });
                            window.requestAnimationFrame(function () {
                                window.scrollTo(0, scrollY);
                            });

                            if (historyMode === 'push') {
                                window.history.pushState({}, '', url.pathname + url.search);
                            } else if (historyMode === 'replace') {
                                var currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('tab', activeTab || 'products');
                                window.history.replaceState({}, '', currentUrl.pathname + currentUrl.search);
                            }
                        } catch (error) {
                            console.error(error);
                            wrapper.setAttribute('aria-busy', 'false');
                            showRequestError('Không thể hoàn tất thao tác. Vui lòng kiểm tra kết nối và thử lại.');
                        } finally {
                            requestPending = false;
                        }
                    };

                    document.addEventListener('submit', function (event) {
                        var form = event.target.closest('.ecommerce-admin form');
                        if (!form) return;
                        event.preventDefault();
                        var activeTab = document.querySelector('.nav-pills .nav-link.active');
                        var tabKey = activeTab ? activeTab.getAttribute('data-tab-key') : 'products';
                        var formData = new FormData(form);
                        if (event.submitter && event.submitter.name) {
                            formData.set(event.submitter.name, event.submitter.value);
                        }
                        formData.set('active_tab', tabKey || 'products');
                        formData.set('scroll_y', String(Math.round(window.scrollY || window.pageYOffset || 0)));
                        var actionUrl = form.getAttribute('action') || window.location.href;
                        loadAdminPage(new URL(actionUrl, window.location.href), 'POST', formData, 'replace', tabKey);
                    });

                    document.addEventListener('click', function (event) {
                        var target = event.target instanceof Element ? event.target : null;
                        var link = target ? target.closest('.ecommerce-admin nav[aria-label="Pagination"] a') : null;
                        if (!link) return;
                        var url = new URL(link.href, window.location.href);
                        if (url.origin !== window.location.origin) return;
                        event.preventDefault();
                        loadAdminPage(url, 'GET', null, 'push', null);
                    });

                    window.addEventListener('popstate', function () {
                        loadAdminPage(new URL(window.location.href), 'GET', null, 'none', null);
                    });

                    <?php if ($restoreScrollY > 0) { ?>
                        window.addEventListener('load', function () {
                            window.scrollTo(0, <?=$restoreScrollY;?>);
                        });
                    <?php } ?>
                })();
            </script>
        <?php } ?>
        </div>
    </section>
</div>
<?php require_once(__DIR__ . "/Footer.php"); ?>
