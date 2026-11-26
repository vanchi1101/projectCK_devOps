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
