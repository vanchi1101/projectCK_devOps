<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");
require_once(__DIR__ . "/../../includes/login-admin.php");

$title = 'Quản trị | ' . $AZCODE->site('tenweb');
$isInstalled = ecom_is_installed();
$activeCategoryCount = 0;
$productCount = 0;
$orderCount = 0;

if ($isInstalled) {
    $categoryCount = $AZCODE->get_row(
        "SELECT COUNT(*) AS `total` FROM `ecom_categories` WHERE `status` = 'active'"
    );
    $productCountRow = $AZCODE->get_row("SELECT COUNT(*) AS `total` FROM `ecom_products`");
    $orderCountRow = $AZCODE->get_row("SELECT COUNT(*) AS `total` FROM `ecom_orders`");
    $activeCategoryCount = (int) ($categoryCount['total'] ?? 0);
    $productCount = (int) ($productCountRow['total'] ?? 0);
    $orderCount = (int) ($orderCountRow['total'] ?? 0);
}

require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Sidebar.php");
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Tổng quan quản trị</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if (!$isInstalled) { ?>
                <div class="alert alert-warning">
                    Chưa khởi tạo các bảng thương mại điện tử. Hãy sao lưu database và import migration theo hướng dẫn của dự án.
                </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?=$activeCategoryCount;?></h3>
                                <p>Danh mục đang hoạt động</p>
                            </div>
                            <div class="icon"><i class="fas fa-tags"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><?=$productCount;?></h3>
                                <p>Tổng sản phẩm</p>
                            </div>
                            <div class="icon"><i class="fas fa-box-open"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?=$orderCount;?></h3>
                                <p>Đơn hàng</p>
                            </div>
                            <div class="icon"><i class="fas fa-shopping-bag"></i></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Khu vực quản trị</h3></div>
                    <div class="card-body">
                        <p>Trang quản trị được tách khỏi cửa hàng công khai. Chọn khu vực cần thao tác:</p>
                        <a class="btn btn-primary mr-2 mb-2" href="<?=BASE_URL('Admin/Ecommerce');?>">
                            <i class="fas fa-store mr-1"></i> Quản lý cửa hàng
                        </a>
                        <a class="btn btn-outline-secondary mr-2 mb-2" href="<?=BASE_URL('Admin/Users');?>">
                            <i class="fas fa-users mr-1"></i> Thành viên
                        </a>
                        <a class="btn btn-outline-secondary mr-2 mb-2" href="<?=BASE_URL('Admin/Bank');?>">
                            <i class="fas fa-university mr-1"></i> Ngân hàng
                        </a>
                        <a class="btn btn-outline-secondary mr-2 mb-2" href="<?=BASE_URL('Admin/Setting');?>">
                            <i class="fas fa-cog mr-1"></i> Cấu hình
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
</div>
<?php require_once(__DIR__ . "/Footer.php"); ?>
