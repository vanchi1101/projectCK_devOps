<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/function.php';

$clientId = azcode_env('GOOGLE_CLIENT_ID');
$clientSecret = azcode_env('GOOGLE_CLIENT_SECRET');
$redirectUri = azcode_env('GOOGLE_REDIRECT_URI', BASE_URL('Auth/Google/Callback'));

if (!$clientId || !$clientSecret) {
    error_log('Google OAuth cannot start because server credentials are missing.');
    $_SESSION['google_login_error'] = 'Không thể bắt đầu đăng nhập Google. Vui lòng thử lại sau.';
    header('Location: ' . BASE_URL('Auth/Login'));
    exit;
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_oauth_state'] = $state;
$_SESSION['google_oauth_state_created_at'] = time();

$authorizationUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
    'prompt' => 'select_account'
], '', '&', PHP_QUERY_RFC3986);

header('Location: ' . $authorizationUrl);
exit;
