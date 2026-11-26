<?php
    require_once("../../config/config.php");
    require_once("../../config/function.php");
    $title = 'QUẢN LÝ THÀNH VIÊN | '.$AZCODE->site('tenweb');
    require_once(__DIR__."/../../includes/login-admin.php");
    require_once(__DIR__."/Header.php");
    require_once(__DIR__."/Sidebar.php");
?>



<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Quản lý thành viên</h1>
                </div>
                <div class="col-sm-6">
                    <button class="float-right btn btn-danger" type="button" onclick="rsTotalMoney()"><i class="fas fa-eraser mr-1"></i>RESET TOP NẠP</button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">DANH SÁCH THÀNH VIÊN</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>USERNAME</th>
                                        <th>MONEY</th>
                                        <th>TOTAL MONEY</th>
                                        <th>CREATEDATE</th>
                                        <th>CTV</th>
                                        <th>CTV BÁN ACC</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $perPage = 20;
                                    $page = max(1, (int) ($_GET['page'] ?? 1));
                                    $countRow = $AZCODE->get_row("SELECT COUNT(*) AS total FROM `users` WHERE `username` IS NOT NULL");
                                    $totalUsers = (int) ($countRow['total'] ?? 0);
                                    $totalPages = max(1, (int) ceil($totalUsers / $perPage));
                                    $page = min($page, $totalPages);
                                    $offset = ($page - 1) * $perPage;
                                    $users = $AZCODE->get_list(
                                        "SELECT * FROM `users` WHERE `username` IS NOT NULL "
                                        . "ORDER BY id DESC LIMIT $offset,$perPage"
                                    );
                                    foreach ($users as $row) {
                                    ?>
                                    <tr>
                                        <td><?=$row['id'];?></td>
                                        <td><?=$row['username'];?></td>
                                        <td><?=format_cash($row['money']);?></td>
                                        <td><?=format_cash($row['total_money']);?></td>
                                        <td><span class="badge badge-dark"><?=$row['createdate'];?></span></td>
                                        <td><?=display_ctv($row['ctv']);?></td>
                                        <td><?=display_ctvacc($row['ctvacc']);?></td>
                                        <td><?=display_banned($row['banned']);?></td>
                                        <td>
                                            <a type="button" href="<?=BASE_URL('Admin/User/Edit/');?><?=$row['id'];?>"
                                                class="btn btn-primary"><i class="fas fa-edit"></i>
                                                <span>EDIT</span></a>
                                        </td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                            <?=pagination_control(BASE_URL('Admin/Users'), $page, $totalUsers, $perPage);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>


<script type="text/javascript">
function rsTotalMoney() {
    cuteAlert({
        type: "question",
        title: "XÁC NHẬN THAY ĐỔI",
        message: "Bạn có chắc chắn muốn đặt lại top nap tiền không ?",
        confirmText: "Đồng Ý",
        cancelText: "Hủy"
    }).then((e) => {
        if (e) {
            $.ajax({
                url: "<?=BASE_URL("assets/ajaxs/admin/rsTotalMoney.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: true
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        cuteToast({
                            type: "success",
                            message: respone.msg,
                            timer: 5000
                        });
                        location.reload();
                    } else {
                        cuteAlert({
                            type: "error",
                            title: "Error",
                            message: respone.msg,
                            buttonText: "Okay"
                        });
                    }
                },
                error: function() {
                    alert(html(response));
                    location.reload();
                }
            });
        }
    })
}
</script>
<script>

</script>



<?php 
    require_once("../../public/admin/Footer.php");
?>