<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/function.php';
$title = 'QUÊN MẬT KHẨU | ' . $AZCODE->site('tenweb');
require_once __DIR__ . '/Header.php';
require_once __DIR__ . '/Nav.php';
require_once __DIR__ . '/AuthStyles.php';
?>

<main class="auth-page">
    <section class="auth-card" aria-labelledby="forgot-password-title">
        <header class="auth-card-header">
            <h1 id="forgot-password-title">Quên mật khẩu?</h1>
            <p>Nhập email đã đăng ký. Nếu email thuộc tài khoản, mã xác minh sẽ được gửi đến hộp thư của bạn.</p>
        </header>
        <div id="thongbao" class="auth-message" role="status" aria-live="polite"></div>
        <form id="forgot-password-form" class="auth-form">
            <label class="auth-field" for="email">
                Địa chỉ email
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       autocomplete="email" maxlength="190" required autofocus>
            </label>
            <button type="submit" id="ForgotPassword" class="auth-submit">Gửi mã xác minh</button>
        </form>
        <p class="auth-card-footer">
            Nhớ mật khẩu?
            <a href="<?=BASE_URL('Auth/Login');?>">Quay lại đăng nhập</a>
        </p>
    </section>
</main>

<script type="text/javascript">
$("#forgot-password-form").on("submit", function(event) {
    event.preventDefault();
    var button = $("#ForgotPassword");
    button.text("Đang gửi...").prop("disabled", true);
    $.ajax({
        url: "<?=BASE_URL("assets/ajaxs/Auth.php");?>",
        method: "POST",
        data: {
            type: "ForgotPassword",
            email: $("#email").val()
        }
    }).done(function(response) {
        $("#thongbao").html(response);
    }).fail(function() {
        $("#thongbao").html('<div class="alert alert-danger" role="alert">Không thể gửi yêu cầu. Vui lòng thử lại.</div>');
    }).always(function() {
        button.text("Gửi mã xác minh").prop("disabled", false);
    });
});
</script>

<?php require_once __DIR__ . '/Footer.php'; ?>
