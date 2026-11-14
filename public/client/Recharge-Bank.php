<?php
    require_once("../../config/config.php");
    require_once("../../config/function.php");
    $title = 'NẠP ATM/MOMO TỰ ĐỘNG| '.$AZCODE->site('tenweb');
    require_once("../../public/client/Header.php");
    require_once("../../public/client/Nav.php");
    CheckLogin();

    // Lấy thông tin tài khoản ngân hàng từ database
    $bank = $AZCODE->get_row("SELECT * FROM `bank` LIMIT 1"); 

    // Kiểm tra nếu có tài khoản ngân hàng
    if ($bank) {
        $bankCode = strtoupper($bank['name']); // Mã ngân hàng viết hoa
        $accountNumber = $bank['stk']; // Số tài khoản
        $accountName = urlencode($bank['bank_name']); // Chủ tài khoản
        $amount = ""; // Số tiền có thể để trống hoặc set mặc định
        $noidung = urlencode($AZCODE->site('noidung_naptien').$getUser['id']); // Nội dung chuyển khoản

        // Tạo URL QR code từ VietQR
        $qrUrl = "https://img.vietqr.io/image/$bankCode-$accountNumber-compact.png?amount=$amount&addInfo=$noidung&accountName=$accountName";
    }
?>
<div class="w-full max-w-6xl mx-auto pt-6 md:pt-8 pb-8">
    <div class="grid grid-cols-8 gap-4 md:p-4 bg-box-dark">
        <?php require_once('Sidebar.php');?>
        <div class="col-span-8 sm:col-span-5 md:col-span-6 lg:col-span-6 xl:col-span-6 px-2 md:px-0">
            <div class="w-full mb-10">
                <h2 class="v-title uppercase border-l-4 border-red-800 px-3 select-none text-white text-xl md:text-2xl font-bold">
                    Nạp tiền qua ATM/MOMO
                </h2>
                <div class="mt-4 text-white">
                    <div class="p-2 border border-gray-300 mb-4">
                        <div class="flex justify-between items-center cursor-pointer">
                            <div class="flex select-none">
                                <img src="<?=BASE_URL('assets/img/');?>bank.png" class="h-10 w-10 rounded">
                                <div class="ml-2 text-left">
                                    <h2 class="font-bold text-base">
                                        Chuyển khoản qua Ngân hàng & Ví điện tử
                                    </h2>
                                    <p class="text-xs">Chuyển khoản ngân hàng online.</p>
                                </div>
                            </div>
                            <button class="select-none focus:outline-none bg-pink-600 text-white text-xs inline-block h-5 flex items-center justify-center font-semibold px-2 rounded">
                                Auto
                            </button>
                        </div>
                        <div>
                            <div class="border-t border-gray-200 mt-2 p-2 select-text">
                                <div class="relative">
                                    <?=$AZCODE->site('luuy_napbank');?>
                                    <p>&nbsp;</p>
                                    <p><strong>THÔNG TIN TÀI KHOẢN NGÂN HÀNG</strong></p>
                                    <?php if ($bank) { ?>
                                        <p><span style="color: green;"><strong>✔ : <?=$bank['name'];?></strong></span></p>
                                        <p><strong>Chủ tài khoản: <?=$bank['bank_name'];?></strong></p>
                                        <p><strong>STK/SDT: </strong>
    <span style="color: yellow;"><strong id="copySTK"><?=$bank['stk'];?></strong></span>
    <button type="button" class="copy ml-1 bg-gray-500 font-semibold text-white rounded focus:outline-none py-1 px-3" data-clipboard-target="#copySTK">
        Sao chép
    </button>
</p>

                                        <p><strong><?=$bank['ghichu'];?></strong></p>
                                        <p>&nbsp;</p>

                                        <!-- Mã QR -->
                                        <div style="text-align: center; margin-top: 10px;">
                                            <p><strong>Quét mã QR để chuyển khoản nhanh:</strong></p>
                                            <img src="<?=$qrUrl;?>" alt="QR Code" style="max-width: 300px; height: auto;">
                                        </div>
                                    <?php } else { ?>
                                        <p style="color: red;">Không có thông tin ngân hàng nào được thiết lập.</p>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 w-full select-text">
                                <div class="p-2">
                                    <div>Nội dung chuyển khoản của bạn:</div>
                                    <div class="my-2 items-center w-full text-center">
                                        <span class="font-bold border-dashed border border-red-600 rounded inline-flex justify-center text-center text-red-500 py-1 rounded px-4">
                                            <b id="copyNoiDung"><?=$AZCODE->site('noidung_naptien').$getUser['id'];?></b>
                                        </span> 
                                        <button type="button" class="copy ml-1 bg-gray-500 font-semibold text-white rounded focus:outline-none py-1 px-3" data-clipboard-target="#copyNoiDung">
                                            Sao chép
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>
<script>
    var clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function(e) {
        alert('Đã sao chép: ' + e.text);
        e.clearSelection();
    });
    clipboard.on('error', function(e) {
        alert('Không thể sao chép, vui lòng thử lại!');
    });
</script>

<?php require_once("../../public/client/Footer.php"); ?>
