<?php
    require_once("../../config/config.php");
    require_once("../../config/function.php");
    $title = 'CẤU HÌNH | '.$AZCODE->site('tenweb');
    require_once(__DIR__."/../../includes/login-admin.php");
    require_once(__DIR__."/Header.php");
    require_once(__DIR__."/Sidebar.php");
    insert_options('home_banners', '');
?>
<?php
if(isset($_POST['btnSaveOption']) && $getUser['level'] == 'admin')
{
    if($AZCODE->site('status_demo') == 'ON')
    {
        admin_msg_warning("Chức năng này không khả dụng trên trang web DEMO!", "", 2000);
    }
    foreach ($_POST as $key => $value)
    {
        $AZCODE->update("options", array(
            'value' => $value
        ), " `name` = '$key' ");
    }
    admin_msg_success('Lưu thành công', '', 500);
}
?>



<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cấu hình</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">CẤU HÌNH THÔNG TIN WEBSITE</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tên website</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="tenweb" value="<?=$AZCODE->site('tenweb');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Mô tả website</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="mota" value="<?=$AZCODE->site('mota');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Từ khóa tìm kiếm</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="tukhoa" value="<?=$AZCODE->site('tukhoa');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Logo website</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="logo" value="<?=$AZCODE->site('logo');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Favicon website</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="favicon" value="<?=$AZCODE->site('favicon');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ảnh giới thiệu website</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="anhbia" value="<?=$AZCODE->site('anhbia');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                           <!-- <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Hotline</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="hotline" value="<?=$AZCODE->site('hotline');?>" 
                                            class="form-control">
                                    </div>
                                </div>
                            </div> -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Facebook</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="facebook" value="<?=$AZCODE->site('facebook');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Zalo</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="zalo" value="<?=$AZCODE->site('zalo');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Telegram</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="telegram" value="<?=$AZCODE->site('telegram');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Discord</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="discord" value="<?=$AZCODE->site('discord');?>"
                                            class="form-control">
                                    </div>
                                </div>
                            </div> -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">ID Video Youtube</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="text" name="id_video_youtube"
                                            value="<?=$AZCODE->site('id_video_youtube');?>" class="form-control"
                                            placeholder="Ví dụ: Zzn9-ATB9aU">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Banner trang chủ</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="home_banners" rows="5"
                                        placeholder="Nhập URL mỗi ảnh trên một dòng"><?=htmlspecialchars((string) $AZCODE->site('home_banners'), ENT_QUOTES, 'UTF-8');?></textarea>
                                    <small class="form-text text-muted">Mỗi dòng là một ảnh banner; nhiều ảnh sẽ tự động chạy luân phiên.</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Email Admin</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <input type="email" name="email_admin" value="<?=$AZCODE->site('email_admin');?>"
                                            class="form-control" placeholder="Nhập Email Admin">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">SMTP Gmail [<a
                                        href="https://www.youtube.com/watch?v=aiMScMCqMIg&list=PLylqe6Lzq69-TzmQ6kLzTg8ZkrxJxxtZm&index=4"
                                        target="_blank">HƯỚNG DẪN</a>]</label>
                                <div class="col-sm-4">
                                    <div class="form-line">
                                        <input type="email" name="email" value="<?=$AZCODE->site('email');?>"
                                            class="form-control" placeholder="Nhập địa chỉ Gmail">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-line">
                                        <input type="password" name="pass_email"
                                            value="<?=htmlspecialchars((string) $AZCODE->site('pass_email'), ENT_QUOTES, 'UTF-8');?>"
                                            autocomplete="new-password"
                                            class="form-control" placeholder="Nhập mật khẩu Gmail">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">ON/OFF Website</label>
                                <div class="col-sm-9">
                                    <select class="form-control show-tick" name="baotri" required>
                                        <option value="<?=$AZCODE->site('baotri');?>"><?=$AZCODE->site('baotri');?>
                                        </option>
                                        <option value="ON">ON</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                    <i>Nếu bạn OFF, website sẽ bật chế độ bảo trì.</i>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Thông báo</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <textarea class="textarea" name="thongbao"
                                            rows="6"><?=$AZCODE->site('thongbao');?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">ON/OFF Block F12</label>
                                <div class="col-sm-9">
                                    <select class="form-control show-tick" name="block_f12" required>
                                        <option value="<?=$AZCODE->site('block_f12');?>"><?=$AZCODE->site('block_f12');?>
                                        </option>
                                        <option value="ON">ON</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                    <i>Nếu bạn ON hệ thống sẽ chặn khách F12</i>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nội dung bên trái dưới Footer</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <textarea class="form-control" name="text_left_footer"
                                            placeholder="Chèn TEXT hoặc HTML nếu có"
                                            rows="4"><?=$AZCODE->site('text_left_footer');?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nội dung ở giữa dưới Footer</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <textarea class="form-control" name="text_center_footer"
                                            placeholder="Chèn TEXT hoặc HTML nếu có"
                                            rows="4"><?=$AZCODE->site('text_center_footer');?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">HTML-JS Footer</label>
                                <div class="col-sm-9">
                                    <div class="form-line">
                                        <textarea class="form-control" rows="10"
                                            style="background-color: #000;color:#00d230;" name="html_footer"
                                            placeholder="Chèn JS trang trí hay chèn code gì vô cũng được nếu muốn"
                                            rows="6"><?=$AZCODE->site('html_footer');?></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="btnSaveOption" class="btn btn-primary btn-block">
                                <span>LƯU</span></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(function() {
    // Summernote
    $('.textarea').summernote()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
        $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    });
})
</script>

<?php 
    require_once("../../public/admin/Footer.php");
?>