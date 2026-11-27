<?php

require_once __DIR__ . '/../includes/ecommerce.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$failures = 0;
$assertSame = static function ($expected, $actual, $message) use (&$failures) {
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL: $message\n");
        fwrite(STDERR, '  Expected: ' . var_export($expected, true) . "\n");
        fwrite(STDERR, '  Actual:   ' . var_export($actual, true) . "\n");
        $failures++;
        return;
    }

    echo "PASS: $message\n";
};

$assertSame(
    ['pending_payment', 'cancelled'],
    ecom_allowed_order_statuses(['status' => 'pending_payment', 'payment_status' => 'unpaid']),
    'unpaid pending orders can be cancelled'
);
$assertSame(
    ['paid', 'processing', 'shipped', 'completed', 'refunded'],
    ecom_allowed_order_statuses(['status' => 'paid', 'payment_status' => 'paid']),
    'paid orders allow fulfillment and refunds'
);
$assertSame(
    ['pending_payment'],
    ecom_allowed_order_statuses(['status' => 'pending_payment', 'payment_status' => 'paid']),
    'paid pending orders cannot be cancelled as unpaid'
);
$assertSame(
    '&lt;script&gt;&quot;x&quot;&lt;/script&gt;',
    ecom_h('<script>"x"</script>'),
    'HTML output is escaped'
);
$assertSame(true, ecom_image_url_is_valid('/assets/storage/images/products/product_2.webp'), 'local product image paths are accepted');
$assertSame(false, ecom_image_url_is_valid('/assets/storage/images/products/../private.png'), 'local product image paths cannot traverse directories');
$assertSame(true, ecom_image_url_is_valid('https://images.example.test/product.jpg'), 'HTTPS image URLs remain accepted');
$assertSame(false, ecom_image_url_is_valid('javascript:alert(1)'), 'non-HTTP image URLs are rejected');

$adminEcommerceSource = file_get_contents(__DIR__ . '/../public/admin/Ecommerce.php');
$storeSource = file_get_contents(__DIR__ . '/../public/client/Store.php');
$assertSame(true, is_string($adminEcommerceSource), 'admin ecommerce source is available for regression checks');
$assertSame(true, is_string($storeSource), 'store source is available for regression checks');
foreach ([
    'admin ecommerce' => $adminEcommerceSource,
    'store filter' => $storeSource
] as $formSourceName => $formSource) {
    if (!is_string($formSource)) {
        continue;
    }

    $assertSame(
        0,
        preg_match('/\bform\.(?:action|method|submit)\b/', $formSource),
        $formSourceName . ' does not read shadowable form action, method, or submit properties'
    );
    $assertSame(
        true,
        strpos($formSource, "form.getAttribute('action') || window.location.href") !== false,
        $formSourceName . ' resolves its endpoint from the action attribute with a same-page fallback'
    );
}

$_SESSION = [];
$token = ecom_csrf_token();
$assertSame(64, strlen($token), 'CSRF token is generated with the expected entropy');
$assertSame($token, ecom_csrf_token(), 'CSRF token remains stable for the session');
$_POST['csrf_token'] = $token;
$assertSame(true, ecom_check_csrf(), 'valid CSRF token is accepted');
$_POST['csrf_token'] = 'invalid-token';
$assertSame(false, ecom_check_csrf(), 'invalid CSRF token is rejected');

if ($failures > 0) {
    fwrite(STDERR, "$failures test(s) failed.\n");
    exit(1);
}

echo "All application tests passed.\n";
