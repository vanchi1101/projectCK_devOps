<?php
        require_once("../../config/config.php");
        require_once("../../config/function.php");
        $title = 'TẠO TÀI KHOẢN | '.$AZCODE->site('tenweb');
        require_once("../../public/client/Header.php");
        require_once("../../public/client/Nav.php");
        require_once(__DIR__ . "/AuthStyles.php");
?>

<main class="auth-page">
    <section class="auth-card" aria-labelledby="register-title">
        <header class="auth-card-header">
            <h1 id="register-title">Tạo tài khoản</h1>
            <p>Đăng ký nhanh chóng để bắt đầu mua sắm.</p>
        </header>
        <div id="thongbao" class="auth-message" role="status" aria-live="polite"></div>
        <form id="register-form" class="auth-form">
            <label class="auth-field" for="email">
                Địa chỉ email
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       autocomplete="email" maxlength="190" required>
            </label>
            <label class="auth-field" for="username">
                Tên đăng nhập
                <input type="text" id="username" name="username" placeholder="Chọn tên đăng nhập"
                       autocomplete="username" required>
            </label>
            <label class="auth-field" for="password">
                Mật khẩu
                <input type="password" id="password" name="password" placeholder="Tạo mật khẩu"
                       autocomplete="new-password" required>
            </label>
            <label class="auth-field" for="repassword">
                Nhập lại mật khẩu
                <input type="password" id="repassword" name="repassword" placeholder="Nhập lại mật khẩu"
                       autocomplete="new-password" required>
            </label>
            <button type="submit" id="Register" class="auth-submit">Tạo tài khoản</button>
        </form>
        <div class="auth-divider" aria-hidden="true"><span>Hoặc</span></div>
        <a class="auth-google-button" href="<?=BASE_URL('Auth/Google');?>">
            <svg viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.76 7.18l7.73 6C44.42 38.05 46.98 31.9 46.98 24.55z"/>
                <path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.9 23.9 0 0 0 0 24c0 3.87.93 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.91-5.8l-7.73-6c-2.15 1.45-4.9 2.3-8.18 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Đăng ký với Google
        </a>
        <p class="auth-card-footer">
            Đã có tài khoản?
            <a href="<?=BASE_URL('Auth/Login');?>">Đăng nhập</a>
        </p>
    </section>
</main>


<script type="text/javascript">
$("#register-form").on("submit", function(event) {
    event.preventDefault();
    var button = $("#Register");
    button.text("Đang tạo tài khoản...").prop("disabled", true);
    $.ajax({
        url: "<?=BASE_URL("assets/ajaxs/Auth.php");?>",
        method: "POST",
        data: {
            type: "Register",
            email: $("#email").val(),
            username: $("#username").val(),
            password: $("#password").val(),
            repassword: $("#repassword").val()
        }
    }).done(function(response) {
        $("#thongbao").html(response);
    }).fail(function() {
        $("#thongbao").html('<div class="alert alert-danger" role="alert">Không thể kết nối. Vui lòng thử lại.</div>');
    }).always(function() {
        button.text("Tạo tài khoản").prop("disabled", false);
    });
});
</script>


<?php 
    require_once("../../public/client/Footer.php");
?>