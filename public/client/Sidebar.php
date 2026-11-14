<?php
$accountPath = strtolower((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$isProfile = strpos($accountPath, '/auth/profile') !== false && strpos($accountPath, 'changepassword') === false;
$isBalance = strpos($accountPath, 'bien-dong-so-du') !== false;
$isPassword = strpos($accountPath, 'changepassword') !== false;
$isBank = strpos($accountPath, 'nap-tien-qua-ngan-hang') !== false;
$isOrders = strpos($accountPath, '/myorders') !== false;
?>
<style>
    .account-sidebar {
        min-width: 0;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
    }
    .account-sidebar .v-menu-account { margin: 0; }
    .account-sidebar .v-menu-account + .v-menu-account { margin-top: 24px; }
    .account-sidebar .v-menu-account h2 {
        margin: 0 0 10px;
        padding: 2px 0 2px 11px;
        border-left: 3px solid #2563eb !important;
        color: #172033 !important;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.4;
    }
    .account-sidebar .v-menu-account ul {
        margin: 0;
        padding: 0;
        color: #334155 !important;
        list-style: none;
    }
    .account-sidebar .v-menu-account li { margin: 3px 0; padding: 0; }
    .account-sidebar .v-menu-account a {
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 40px;
        padding: 8px 10px;
        border-radius: 7px;
        color: #334155 !important;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.35;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .account-sidebar .v-menu-account a:hover { background: #f1f5f9; color: #1d4ed8 !important; }
    .account-sidebar .v-menu-account a[aria-current="page"] {
        background: #eff6ff;
        color: #1d4ed8 !important;
        font-weight: 700;
    }
    .account-sidebar .v-menu-account a i { width: 18px; color: #64748b; text-align: center; }
    .account-sidebar .v-menu-account a[aria-current="page"] i { color: #2563eb; }
    @media (max-width: 639px) {
        .account-sidebar { padding: 16px; }
        .account-sidebar .v-menu-account ul { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px; }
        .account-sidebar .v-menu-account + .v-menu-account { margin-top: 18px; }
    }
    @media (max-width: 480px) {
        .account-sidebar .v-menu-account ul { grid-template-columns: 1fr; }
    }
</style>

<aside class="account-sidebar col-span-8 sm:col-span-3 md:col-span-2 lg:col-span-2 xl:col-span-2">
    <div class="v-menu-account">
        <h2>Tài khoản</h2>
        <ul>
            <li><a href="<?=BASE_URL('Auth/Profile');?>" <?=$isProfile ? 'aria-current="page"' : '';?>>
                <i class="bx bxs-user-circle" aria-hidden="true"></i><span>Thông tin tài khoản</span>
            </a></li>
            <li><a href="<?=BASE_URL('bien-dong-so-du/');?>" <?=$isBalance ? 'aria-current="page"' : '';?>>
                <i class="bx bxs-dollar-circle" aria-hidden="true"></i><span>Biến động số dư</span>
            </a></li>
            <li><a href="<?=BASE_URL('Auth/Profile/ChangePassword');?>" <?=$isPassword ? 'aria-current="page"' : '';?>>
                <i class="bx bxs-lock" aria-hidden="true"></i><span>Đổi mật khẩu</span>
            </a></li>
        </ul>
    </div>
    <div class="v-menu-account">
        <h2>Giao dịch</h2>
        <ul>
            <li><a href="<?=BASE_URL('nap-tien-qua-ngan-hang/');?>" <?=$isBank ? 'aria-current="page"' : '';?>>
                <i class="bx bxs-credit-card" aria-hidden="true"></i><span>Nạp ví qua ngân hàng</span>
            </a></li>
            <li><a href="<?=BASE_URL('MyOrders');?>" <?=$isOrders ? 'aria-current="page"' : '';?>>
                <i class="bx bxs-receipt" aria-hidden="true"></i><span>Lịch sử đơn hàng</span>
            </a></li>
        </ul>
    </div>
</aside>