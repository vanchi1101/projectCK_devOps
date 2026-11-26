<?php
        require_once("../../config/config.php");
        require_once("../../config/function.php");
        $title = 'ĐĂNG NHẬP | '.$AZCODE->site('tenweb');
        require_once("../../public/client/Header.php");
        require_once("../../public/client/Nav.php");
        require_once(__DIR__ . "/AuthStyles.php");
        $googleLoginError = $_SESSION['google_login_error'] ?? '';
        unset($_SESSION['google_login_error']);
?>

<main class="auth-page">
    <section class="auth-card" aria-labelledby="login-title">
        <header class="auth-card-header">
            <h1 id="login-title">Chào mừng trở lại</h1>
            <p>Đăng nhập để tiếp tục với tài khoản của bạn.</p>
        </header>
        <?php if ($googleLoginError !== '') { ?>
            <div class="alert alert-warning auth-message" role="alert"><?=htmlspecialchars($googleLoginError, ENT_QUOTES, 'UTF-8');?></div>
        <?php } ?>
        <div id="thongbao" class="auth-message" role="status" aria-live="polite"></div>
        <form id="login-form" class="auth-form">
            <label class="auth-field" for="username">
                Tên đăng nhập
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập"
                       autocomplete="username" required>
            </label>
            <label class="auth-field" for="password">
                <span class="auth-password-row">
                    <span>Mật khẩu</span>
                    <a href="<?=BASE_URL('Auth/ForgotPassword');?>">Quên mật khẩu?</a>
                </span>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu"
                       autocomplete="current-password" required>
            </label>
            <button type="submit" id="Login" class="auth-submit">Đăng nhập</button>
        </form>
        <div class="auth-divider" aria-hidden="true"><span>Hoặc</span></div>
        <a class="auth-google-button" href="<?=BASE_URL('Auth/Google');?>">
            <svg viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 38.05 46.98 31.9 46.98 24.55z"/>
                <path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.87.93 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.15 1.45-4.9 2.3-8.18 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Tiếp tục với Google
        </a>
        <p class="auth-card-footer">
            Chưa có tài khoản?
            <a href="<?=BASE_URL('Auth/Register');?>">Tạo tài khoản</a>
        </p>
    </section>
</main>


<script type="text/javascript">
$("#login-form").on("submit", function(event) {
    event.preventDefault();
    var button = $("#Login");
    button.text("Đang đăng nhập...").prop("disabled", true);
    $.ajax({
        url: "<?=BASE_URL("assets/ajaxs/Auth.php");?>",
        method: "POST",
        data: {
            type: "Login",
            username: $("#username").val(),
            password: $("#password").val()
        }
    }).done(function(response) {
        $("#thongbao").html(response);
    }).fail(function() {
        $("#thongbao").html('<div class="alert alert-danger" role="alert">Không thể kết nối. Vui lòng thử lại.</div>');
    }).always(function() {
        button.text("Đăng nhập").prop("disabled", false);
    });
});
</script>


<?php 
    require_once("../../public/client/Footer.php");
?>