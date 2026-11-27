<style>
.zivhd-buttons{
         background: #111;
    border-radius: 5px;
    font-family: 'Sriracha', cursive;
    color: white;
    text-align: center;
    width: 100%;
    text-shadow: -1px 1px 6px white;
        padding-left: 70px;
    padding-right: 70px;
    padding-top: 10px;
    padding-bottom: 10px;
    text-transform: uppercase;
}
.darkshizivhd-box {
   box-shadow: -6px 6px 15px #14234e, 7px -9px 15px #202243, 5px 2px 15px #302c64, 0 0 15px #2c3a64;
    border-radius: 6px;
    backdrop-filter: brightness(120%) blur(5px) hue-rotate(354deg);
    background: #20202a;
}
.robuxvn-box{
    box-shadow: 0 0 10px blue;
    border: solid darkblue 2px;
    border-radius: 5px;
    transition: all 1s;
    margin:5px;
}
.robuxvn-box:hover{
    box-shadow: 0px -5px 8px 0px blue;
      border: solid blue 2px;
    transform: translateY(-6px);
    transition: all 1s;
  
}
.robuxvn-buybutton{
    padding: 5px;
    background: #333;
    width: 100px;
    color: white;
    text-transform: uppercase;
    font-family: 'Black Ops One';
    border-radius: 2.5px;
    transition: all 1s;
}
.content-wrapper-header {
  display: flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
  background-image: url("https://i.imgur.com/t8e66TI.jpeg"), linear-gradient(to right, #0f2027, #203a43, #2c5364);
  border-radius: 14px;
  padding: 20px 40px;
}
@media screen and (max-width: 415px) {
  .content-wrapper-header {
    padding: 20px;
  }
}
.content-wrapper.overlay {
  pointer-events: none;
  transition: 0.3s;
  background-color: var(--overlay-bg);
}
.content-wrapper-img {
  width: 186px;
  -o-object-fit: cover;
     object-fit: cover;
  margin-top: -25px;
  -o-object-position: center;
     object-position: center;
}
@media screen and (max-width: 570px) {
  .content-wrapper-img {
    width: 110px;
  }
}
.img-content {
  font-weight: 500;
  font-size: 17px;
  display: flex;
  align-items: center;
  margin: 0;
}
.img-content svg {
  width: 28px;
  margin-right: 14px;
}
.content-text {
  font-weight: 400;
  font-size: 14px;
  margin-top: 16px;
  line-height: 1.7em;
  color: #ebecec;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
      font-family: 'Roboto';
    text-transform: capitalize;
}
.content-button{
    background-color: #3a6df0;
    border: none;
    padding: 8px 26px;
    color: #fff;
    border-radius: 20px;
    margin-top: 16px;
    cursor: pointer;
    transition: 0.3s;
    white-space: nowrap;
}
</style>
    
<style>
    ::-webkit-scrollbar {
            width: 5px;
        }
          
        ::-webkit-scrollbar-track {
            background: blue;
        }
          
        ::-webkit-scrollbar-thumb {
            background: darkblue;
        }
          
        ::-webkit-scrollbar-thumb:hover {
            background: lightblue
        }
    
</style>
<style>
    .site-page-shell {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    body {
        min-height: 100vh;
        background:
            radial-gradient(ellipse at 12% 8%, rgba(37, 99, 155, .32), transparent 34%),
            radial-gradient(ellipse at 90% 30%, rgba(13, 148, 136, .16), transparent 32%),
            linear-gradient(135deg, #0b1424 0%, #12243b 52%, #0c1a2c 100%);
        background-attachment: fixed;
    }
</style>
<style>
    .site-header-shell {
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 14px 18px;
        background: rgba(8, 18, 32, .78);
        backdrop-filter: blur(14px);
    }
    .site-header {
        display: flex;
        width: min(1200px, 100%);
        min-height: 70px;
        flex-wrap: wrap;
        align-items: center;
        gap: 18px;
        margin: 0 auto;
        padding: 10px 18px;
        border: 1px solid rgba(148, 190, 225, .18);
        border-radius: 18px;
        background: linear-gradient(120deg, rgba(17, 35, 57, .98), rgba(15, 54, 72, .96));
        box-shadow: 0 14px 36px rgba(0, 0, 0, .22);
    }
    .site-header-brand { display: inline-flex; flex: 0 0 auto; align-items: center; }
    .site-header-brand img { display: block; width: auto; max-width: 180px; max-height: 48px; object-fit: contain; }
    .site-header-quick { display: flex; align-items: center; gap: 9px; margin-left: auto; }
    .site-header-quick-link, .site-header-toggle {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 13px;
        border: 1px solid rgba(203, 224, 242, .28);
        border-radius: 12px;
        background: rgba(255, 255, 255, .06);
        color: #f0f8ff;
        font-size: .84rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease, transform .2s ease;
    }
    .site-header-quick-link:hover, .site-header-toggle:hover {
        transform: translateY(-1px);
        border-color: rgba(125, 211, 252, .6);
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }
    .site-header-toggle { display: none; width: 42px; padding: 0; font-size: 1.2rem; }
    .site-header-menu { flex: 1 1 auto; }
    .site-header-menu.hidden { display: none; }
    .site-header-menu nav { width: 100%; }
    .site-header-menu ul {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .site-header-menu li { margin: 0; }
    .site-header-menu a {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        gap: 7px;
        padding: 0 11px;
        border-radius: 10px;
        color: #d7e6f4;
        font-size: .78rem;
        font-weight: 750;
        letter-spacing: .025em;
        text-decoration: none;
        transition: background .18s ease, color .18s ease;
    }
    .site-header-menu a:hover { background: rgba(255, 255, 255, .09); color: #fff; }
    .site-header-menu .site-menu-action a {
        min-height: 38px;
        margin-left: 4px;
        padding: 0 13px;
        border: 1px solid rgba(203, 224, 242, .28);
        background: rgba(255, 255, 255, .05);
    }
    .site-header-menu .site-menu-action a.site-menu-primary {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #2878e8, #16a1a1);
        color: #fff;
    }
    .site-header-menu .site-menu-action a.site-menu-primary:hover { filter: brightness(1.08); }
    @media (max-width: 1023.98px) {
        .site-header-shell { padding: 10px 12px; }
        .site-header { gap: 10px; padding: 9px 12px; border-radius: 15px; }
        .site-header-brand img { max-width: 145px; max-height: 42px; }
        .site-header-toggle { display: inline-flex; }
        .site-header-menu:not(.hidden) { display: flex; }
        .site-header-menu { flex-basis: 100%; }
        .site-header-menu nav, .site-header-menu ul { width: 100%; }
        .site-header-menu ul { align-items: stretch; flex-direction: column; gap: 3px; }
        .site-header-menu li, .site-header-menu a { width: 100%; }
        .site-header-menu a { justify-content: flex-start; min-height: 42px; padding: 0 12px; }
        .site-header-menu .site-menu-action a { justify-content: center; margin: 4px 0 0; }
        .site-header-menu li.site-menu-action { display: inline-flex; flex: 1; }
    }
    @media (max-width: 420px) {
        .site-header-shell { padding: 8px; }
        .site-header { padding: 8px 10px; }
        .site-header-brand img { max-width: 118px; }
        .site-header-quick-link { min-height: 38px; padding: 0 9px; font-size: .76rem; }
        .site-header-toggle { width: 38px; min-height: 38px; }
    }
    @media (min-width: 1024px) {
        .site-header-menu { display: flex !important; }
    }
</style>
<body>
    <div class="site-page-shell">
        <div class="site-header-shell">
            <header class="site-header">
                <a href="<?=BASE_URL('');?>" class="site-header-brand" aria-label="Trang chủ">
                    <img src="<?=htmlspecialchars((string) $AZCODE->site('logo'), ENT_QUOTES, 'UTF-8');?>"
                        alt="<?=htmlspecialchars((string) $AZCODE->site('tenweb'), ENT_QUOTES, 'UTF-8');?>">
                </a>
                <div class="site-header-quick">
                    <label for="menu-toggle" id="toggle" class="site-header-toggle" aria-label="Mở menu">
                        <i class="bx bx-menu"></i>
                    </label>
                </div>
                <div class="site-header-menu hidden" id="menu-toggle">
                    <nav aria-label="Điều hướng chính">
                        <ul>
                            <li><a href="<?=BASE_URL('Shop');?>"><i class="bx bx-store"></i> Cửa hàng</a></li>
                            <li><a href="<?=BASE_URL('Cart');?>"><i class="bx bx-cart"></i> Giỏ hàng (<?=function_exists('ecom_cart') ? array_sum(ecom_cart()) : 0;?>)</a></li>
                            <?php if (!empty($_SESSION['username'])) { ?>
                                <li><a href="<?=BASE_URL('MyOrders');?>"><i class="bx bx-package"></i> Đơn hàng</a></li>
                                <?php if (($getUser['level'] ?? '') !== 'admin' && (int) ($getUser['ctv'] ?? 0) !== 1) { ?>
                                    <li><a href="<?=BASE_URL('Affiliate');?>"><i class="bx bx-share-alt"></i> Tiếp thị liên kết</a></li>
                                <?php } ?>
                            <?php } ?>
                            <?php if (isset($_SESSION['username']) && ($getUser['level'] ?? '') === 'admin') { ?>
                                <li><a href="<?=BASE_URL('admin/');?>"><i class="bx bx-cog"></i> Quản trị website</a></li>
                            <?php } ?>
                            <?php if (isset($_SESSION['username']) && (int) ($getUser['ctv'] ?? 0) === 1) { ?>
                                <li><a href="<?=BASE_URL('public/ctv/Products.php');?>"><i class="bx bx-grid-alt"></i> Panel CTV</a></li>
                            <?php } ?>
                            <?php if (empty($_SESSION['username'])) { ?>
                                <li class="site-menu-action"><a href="<?=BASE_URL('Auth/Login');?>"><i class="bx bx-log-in"></i> Đăng nhập</a></li>
                                <li class="site-menu-action"><a class="site-menu-primary" href="<?=BASE_URL('Auth/Register');?>"><i class="bx bx-user-plus"></i> Đăng ký</a></li>
                            <?php } else { ?>
                                <li class="site-menu-action"><a class="site-menu-primary" href="<?=BASE_URL('Auth/Logout');?>"><i class="bx bx-log-out"></i> Đăng xuất</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </header>
        </div>
        <?php
        