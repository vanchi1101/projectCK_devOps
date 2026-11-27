<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/function.php';

function google_login_fail($message, $diagnostic = '')
{
    if ($diagnostic !== '') {
        error_log('Google OAuth: ' . $diagnostic);
    }

    $_SESSION['google_login_error'] = $message;
    header('Location: ' . BASE_URL('Auth/Login'));
    exit;
}

function google_oauth_request($url, $postFields = null, $headers = [])
{
    $curl = curl_init($url);
    if ($curl === false) {
        return ['ok' => false, 'error' => 'Unable to initialize cURL'];
    }

    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => $headers
    ];

    if ($postFields !== null) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($postFields, '', '&', PHP_QUERY_RFC3986);
    }

    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    unset($curl);

    if ($body === false) {
        return ['ok' => false, 'error' => $error ?: 'Request failed'];
    }

    $data = json_decode($body, true);
    if ($status < 200 || $status >= 300 || !is_array($data)) {
        return ['ok' => false, 'error' => 'Google returned HTTP ' . $status];
    }

    return ['ok' => true, 'data' => $data];
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    google_login_fail('Yêu cầu đăng nhập Google không hợp lệ.');
}

$returnedState = (string) ($_GET['state'] ?? '');
$expectedState = (string) ($_SESSION['google_oauth_state'] ?? '');
$stateCreatedAt = (int) ($_SESSION['google_oauth_state_created_at'] ?? 0);
unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_state_created_at']);

if (
    $returnedState === ''
    || $expectedState === ''
    || !hash_equals($expectedState, $returnedState)
    || $stateCreatedAt < time() - 600
) {
    google_login_fail('Phiên đăng nhập Google đã hết hạn hoặc không hợp lệ. Vui lòng thử lại.');
}

if (isset($_GET['error'])) {
    google_login_fail('Bạn đã hủy đăng nhập Google hoặc không cấp quyền truy cập.');
}

$code = (string) ($_GET['code'] ?? '');
$clientId = azcode_env('GOOGLE_CLIENT_ID');
$clientSecret = azcode_env('GOOGLE_CLIENT_SECRET');
$redirectUri = azcode_env('GOOGLE_REDIRECT_URI', BASE_URL('Auth/Google/Callback'));

if ($code === '' || !$clientId || !$clientSecret) {
    google_login_fail('Không thể hoàn tất đăng nhập Google. Vui lòng thử lại.');
}

$tokenResponse = google_oauth_request('https://oauth2.googleapis.com/token', [
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code'
], ['Accept: application/json']);

if (!$tokenResponse['ok'] || empty($tokenResponse['data']['access_token'])) {
    $diagnostic = $tokenResponse['error'] ?? 'Google did not return an access token';
    google_login_fail('Không thể xác thực tài khoản Google. Vui lòng thử lại.', $diagnostic);
}

$profileResponse = google_oauth_request(
    'https://openidconnect.googleapis.com/v1/userinfo',
    null,
    [
        'Accept: application/json',
        'Authorization: Bearer ' . $tokenResponse['data']['access_token']
    ]
);

if (!$profileResponse['ok']) {
    google_login_fail('Không thể tải thông tin tài khoản Google. Vui lòng thử lại.', $profileResponse['error']);
}

$profile = $profileResponse['data'];
$googleSub = (string) ($profile['sub'] ?? '');
$email = strtolower(trim((string) ($profile['email'] ?? '')));
$emailVerified = ($profile['email_verified'] ?? false) === true
    || ($profile['email_verified'] ?? false) === 'true';

if ($googleSub === '' || !$emailVerified || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    google_login_fail('Tài khoản Google cần có địa chỉ email hợp lệ đã được xác minh.');
}

$googleSubSql = $AZCODE->escape($googleSub);
$user = $AZCODE->get_row("SELECT * FROM `users` WHERE `google_sub` = '$googleSubSql' LIMIT 1");

if ($user) {
    if ((int) ($user['banned'] ?? 0) === 1) {
        google_login_fail('Tài khoản này đã bị khóa bởi quản trị viên.');
    }
} else {
    $emailSql = $AZCODE->escape($email);
    $existingEmailUser = $AZCODE->get_row("SELECT `username` FROM `users` WHERE `email` = '$emailSql' LIMIT 1");

    if ($existingEmailUser) {
        google_login_fail('Email này đã có tài khoản. Hãy đăng nhập bằng tài khoản hiện tại; Google không tự gộp các tài khoản.');
    }

    if ($AZCODE->num_rows("SELECT `id` FROM `users` WHERE `ip` = '" . $AZCODE->escape(myip()) . "'") > 10) {
        google_login_fail('Bạn đã đạt giới hạn tạo tài khoản từ địa chỉ mạng này.');
    }

    $username = 'g' . substr(bin2hex(random_bytes(8)), 0, 15);
    $created = $AZCODE->insert('users', [
        'username' => $username,
        'password' => TypePassword(md5(bin2hex(random_bytes(32)))),
        'token' => bin2hex(random_bytes(32)),
        'email' => $email,
        'google_sub' => $googleSub,
        'level' => '0',
        'money' => 0,
        'total_money' => 0,
        'banned' => 0,
        'ref' => '',
        'ip' => myip(),
        'time' => time(),
        'createdate' => gettime()
    ]);

    if (!$created) {
        error_log('Google OAuth: failed to create a customer account');
        google_login_fail('Không thể tạo tài khoản lúc này. Vui lòng thử lại sau.');
    }

    $user = $AZCODE->get_row("SELECT * FROM `users` WHERE `google_sub` = '$googleSubSql' LIMIT 1");
    if (!$user) {
        google_login_fail('Không thể hoàn tất đăng nhập. Vui lòng thử lại sau.');
    }
}

session_regenerate_id(true);
$_SESSION['username'] = $user['username'];
$destination = ($user['level'] ?? '') === 'admin' ? 'Admin/Home' : '';
header('Location: ' . BASE_URL($destination));
exit;
