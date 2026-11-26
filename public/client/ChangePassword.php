<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/function.php';
$title = 'ĐẶT LẠI MẬT KHẨU | ' . $AZCODE->site('tenweb');
require_once __DIR__ . '/Header.php';
require_once __DIR__ . '/Nav.php';
require_once __DIR__ . '/AuthStyles.php';
?>

<main class="auth-page">
    <section class="auth-card" aria-labelledby="change-password-title">
        <header class="auth-card-header">
            <h1 id="change-password-title">Đặt lại mật khẩu</h1>
            <p>Nhập mã 6 chữ số trong email và tạo mật khẩu mới. Mã hết hạn sau 10 phút.</p>
        </header>
        <div id="thongbao" class="auth-message" role="status" aria-live="polite"></div>
        <form id="change-password-form" class="auth-form">
            <label class="auth-field" for="otp">
                Mã xác minh
                <input type="text" id="otp" name="otp" placeholder="Nhập mã 6 chữ số"
                       inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required>
            </label>
            <label class="auth-field" for="password">
                Mật khẩu mới
                <input type="password" id="password" name="password" placeholder="Tối thiểu 8 ký tự"
                       autocomplete="new-password" minlength="8" required>
            </label>
            <label class="auth-field" for="repassword">
                Nhập lại mật khẩu mới
                <input type="password" id="repassword" name="repassword" placeholder="Nhập lại mật khẩu"
                       autocomplete="new-password" minlength="8" required>
            </label>
            <button type="submit" id="ChangePassword" class="auth-submit">Cập nhật mật khẩu</button>
        </form>
        <p class="auth-card-footer">
            Chưa nhận được mã?
            <a href="<?=BASE_URL('Auth/ForgotPassword');?>">Gửi lại mã</a>
        </p>
    </section>
</main>

<script type="text/javascript">
$("#change-password-form").on("submit", function(event) {
    event.preventDefault();
    var button = $("#ChangePassword");
    button.text("Đang cập nhật...").prop("disabled", true);
    $.ajax({
        url: "<?=BASE_URL("assets/ajaxs/Auth.php");?>",
        method: "POST",
        data: {
            type: "ChangePassword",
            otp: $("#otp").val(),
            password: $("#password").val(),
            repassword: $("#repassword").val()
        }
    }).done(function(response) {
        $("#thongbao").html(response);
    }).fail(function() {
        $("#thongbao").html('<div class="alert alert-danger" role="alert">Không thể cập nhật mật khẩu. Vui lòng thử lại.</div>');
    }).always(function() {
        button.text("Cập nhật mật khẩu").prop("disabled", false);
    });
});
</script>

<?php require_once __DIR__ . '/Footer.php'; ?>
