<?php 
    require_once("../../config/config.php");
    require_once("../../config/function.php");
    require_once("../../includes/ecommerce.php");
    require_once('../../class/class.smtp.php');
    require_once('../../class/PHPMailerAutoload.php');
    require_once('../../class/class.phpmailer.php');

    $type = (string) ($_POST['type'] ?? '');

    if($type === 'Login')
    {
        $username = check_string($_POST['username']);
        $password = TypePassword(check_string($_POST['password']));
        if(empty($username))
        {
            msg_error2("Vui lòng nhập tên đăng nhập !");
        }
        if(!$AZCODE->get_row(" SELECT * FROM `users` WHERE `username` = '$username' "))
        {
            msg_error2('Tên đăng nhập không tồn tại');
        }
        if(empty($password))
        {
            msg_error2("Vui lòng nhập mật khẩu !");
        }
        if($AZCODE->get_row(" SELECT * FROM `users` WHERE `username` = '$username' AND `banned` = '1' "))
        {
            msg_error2('Tài khoản này đã bị khóa bởi BQT');
        }
        $user = $AZCODE->get_row(
            "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '".md5($password)."'"
        );
        if(!$user)
        {
            msg_error2('Mật khẩu đăng nhập không chính xác');
        }
        $AZCODE->update("users", [
            'otp' => NULL,
            'otp_created_at' => NULL
        ], " `username` = '$username' ");
        $_SESSION['username'] = $username;
        $redirect = $user['level'] === 'admin' ? BASE_URL('Admin/Home') : BASE_URL('');
        msg_success('Đăng nhập thành công ', $redirect, 0);
    }

    if($type === 'Register')
    {
        $email = strtolower(trim(check_string($_POST['email'] ?? '')));
        $username = check_string($_POST['username']);
        $password = check_string($_POST['password']);
        $repassword = check_string($_POST['repassword']);
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 190)
        {
            msg_error2('Vui lòng nhập địa chỉ email hợp lệ');
        }
        $emailSql = $AZCODE->escape($email);
        if($AZCODE->get_row("SELECT `id` FROM `users` WHERE `email` = '$emailSql' LIMIT 1"))
        {
            msg_error2('Địa chỉ email đã được sử dụng');
        }
        if(empty($username))
        {
            msg_error2("Vui lòng nhập tên tài khoản !");
        }
        if(check_username($username) != True)
        {
            msg_error2('Vui lòng nhập định dạng tài khoản hợp lệ');
        }
        if(strlen($username) < 5 || strlen($username) > 64)
        {
            msg_error2('Tài khoản phải từ 5 đến 64 ký tự');
        }
        if($AZCODE->get_row(" SELECT * FROM `users` WHERE `username` = '$username' "))
        {
            msg_error2('Tên đăng nhập đã tồn tại!');
        }
        if(empty($password))
        {
            msg_error2("Vui lòng nhập mật khẩu !");
        }
        if(strlen($password) < 3)
        {
            msg_error2('Vui lòng đặt mật khẩu 3 ký tự trở lên');
        }
        if($password != $repassword)
        {
            msg_error2('Nhập lại mật khẩu không đúng');
        }
        if($AZCODE->num_rows(" SELECT * FROM `users` WHERE `ip` = '".myip()."' ") > 10)
        {
            msg_error2('Bạn đã đạt giới hạn tạo tài khoản');
        }
        $referrerId = isset($_SESSION['ecom_referrer_id']) ? (int) $_SESSION['ecom_referrer_id'] : 0;
        if ($referrerId <= 0 || ecom_commission_kind_for_user($referrerId) !== 'referral') {
            $referrerId = '';
        }
        $create = $AZCODE->insert("users", [
            'username'      => $username,
            'email'         => $email,
            'password'      => TypePassword(md5($password)),
            'token'         => random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM', 64),
            'money'         => 0,
            'total_money'   => 0,
            'banned'        => 0,
            'ref'           => $referrerId,
            'ip'            => myip(),
            'time'          => time(),
            'createdate'    => gettime()
        ]);
        if ($create)
        {   
            $_SESSION['username'] = $username;
            msg_success('Tạo tài khoản thành công', BASE_URL(''), 1000);
        }
        else
        {
            msg_error2('Vui lòng kiểm tra cấu hình DATABASE');
        }
    }


    if($type === 'ForgotPassword')
    {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
        {
            msg_error2('Vui lòng nhập địa chỉ email hợp lệ');
        }
        if (trim((string) $AZCODE->site('email')) === '' || trim((string) $AZCODE->site('pass_email')) === '')
        {
            error_log('Password reset: SMTP is not configured.');
            msg_error2('Dịch vụ email hiện không khả dụng. Vui lòng liên hệ quản trị viên.');
        }
        $emailSql = $AZCODE->escape($email);
        $row = $AZCODE->get_row("SELECT `id`,`email` FROM `users` WHERE `email` = '$emailSql' LIMIT 1");
        if($row)
        {
            unset($_SESSION['countVeri']);
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpHash = hash('sha256', $otp);
            $otpHashSql = $AZCODE->escape($otpHash);
            $updated = $AZCODE->update("users", [
                'otp' => $otpHash,
                'otp_created_at' => time()
            ], " `id` = '".(int) $row['id']."' ");

            if (!$updated)
            {
                error_log('Password reset: failed to save a verification code.');
                msg_error2('Không thể tạo mã xác minh. Vui lòng thử lại.');
            }
            else
            {
                $subject = 'XÁC NHẬN KHÔI PHỤC MẬT KHẨU';
                $siteName = htmlspecialchars((string) $AZCODE->site('tenweb'), ENT_QUOTES, 'UTF-8');
                $body = '<h3>Yêu cầu đặt lại mật khẩu</h3>'
                    . '<p>Mã xác minh của bạn có hiệu lực trong 10 phút:</p>'
                    . '<p style="font-size:28px;font-weight:bold;letter-spacing:5px;">'
                    . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . '</p>'
                    . '<p>Nếu bạn không yêu cầu, hãy bỏ qua email này.</p>';

                if (!sendCSM($email, 'Khách hàng', $subject, $body, $siteName))
                {
                    $AZCODE->update("users", [
                        'otp' => NULL,
                        'otp_created_at' => NULL
                    ], " `id` = '".(int) $row['id']."' AND `otp` = '$otpHashSql' ");
                    error_log('Password reset: verification email could not be sent.');
                    msg_error2('Không thể gửi email mã xác minh. Vui lòng thử lại sau.');
                }
            }
        }
        msg_success('Nếu email có trong hệ thống, mã xác minh sẽ được gửi đến hộp thư của bạn.', BASE_URL('Auth/ChangePassword'), 4000);
    }

    if($type === 'ChangePassword')
    {
        $otp = trim((string) ($_POST['otp'] ?? ''));
        $repassword = check_string((string) ($_POST['repassword'] ?? ''));
        $password = check_string((string) ($_POST['password'] ?? ''));
        if(empty($otp))
        {
            msg_error2("Bạn chưa nhập OTP");
        }
        if(empty($password))
        {
            msg_error2("Bạn chưa nhập mật khẩu mới");
        }
        if(empty($repassword))
        {
            msg_error2("Vui lòng xác minh lại mật khẩu");
        }
        if(isset($_SESSION['countVeri']))
        {
            if($_SESSION['countVeri'] >= 3)
            {
                msg_error2("Chức năng này tạm khóa");
            }
        }
        else
        {
            $_SESSION['countVeri'] = 0;
        }
        if($password != $repassword)
        {
            msg_error2("Nhập lại mật khẩu không đúng");
        }
        if(strlen($password) < 8)
        {
            msg_error2('Mật khẩu phải có ít nhất 8 ký tự');
        }
        if (!preg_match('/^\d{6}$/', $otp))
        {
            $_SESSION['countVeri']++;
            msg_error2('Mã xác minh không hợp lệ hoặc đã hết hạn');
        }
        $otpHash = $AZCODE->escape(hash('sha256', $otp));
        $validAfter = time() - 600;
        $row = $AZCODE->get_row(
            "SELECT `id` FROM `users` WHERE `otp` = '$otpHash' AND `otp_created_at` >= '$validAfter' LIMIT 1"
        );
        if(!$row)
        {
            $_SESSION['countVeri']++;
            msg_error2('Mã xác minh không hợp lệ hoặc đã hết hạn');
        }
        $updated = $AZCODE->update("users", [
            'otp' => NULL,
            'otp_created_at' => NULL,
            'password' => TypePassword(md5($password))
        ], " `id` = '".(int) $row['id']."' AND `otp` = '$otpHash' ");
        if (!$updated)
        {
            msg_error2('Không thể cập nhật mật khẩu. Vui lòng thử lại.');
        }
        unset($_SESSION['countVeri']);
        msg_success2("Mật khẩu của bạn đã được thay đổi thành công !");
    }

    if($type === 'DoiMatKhau')
    {
        if($AZCODE->site('status_demo') == 'ON')
        {
            msg_error2("Chức năng này không khả dụng trên trang web DEMO!");
        }
        $repassword = check_string($_POST['repassword']);
        $password = check_string($_POST['password']);
        if(empty($password))
        {
            msg_error2("Bạn chưa nhập mật khẩu mới");
        }
        if(empty($repassword))
        {
            msg_error2("Vui lòng xác minh lại mật khẩu");
        }
        if($password != $repassword)
        {
            msg_error2("Nhập lại mật khẩu không đúng");
        }
        if(strlen($password) < 5)
        {
            msg_error2('Vui lòng nhập mật khẩu có ích nhất 5 ký tự');
        }
        $row = $AZCODE->get_row(" SELECT * FROM `users` WHERE `username` = '".$_SESSION['username']."' ");
        if(!$row)
        {
            msg_error("Vui lòng đăng nhập!", BASE_URL(''), 2000);
        }
        $AZCODE->update("users", [
            'otp' => NULL,
            'otp_created_at' => NULL,
            'password' => TypePassword(md5($password))
        ], " `id` = '".$row['id']."' ");
        msg_success("Mật khẩu của bạn đã được thay đổi thành công !", "", 1000);
    }