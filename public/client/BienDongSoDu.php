<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
CheckLogin();

$title = 'BIẾN ĐỘNG SỐ DƯ | ' . $AZCODE->site('tenweb');
$username = $AZCODE->escape($getUser['username']);
$transactions = $AZCODE->get_list(
    "SELECT * FROM `dongtien` WHERE `username` = '$username' ORDER BY `id` DESC"
);
require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");
?>
<style>
    .balance-page { max-width: 1280px; margin: 0 auto; padding: 28px 16px 40px; }
    .balance-layout { align-items: start; }
    .balance-content { min-width: 0; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; color: #172033; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); }
    .balance-title { margin: 0; color: #172033; font-size: 24px; font-weight: 700; }
    .balance-subtitle { margin: 6px 0 22px; color: #64748b; font-size: 14px; }
    .balance-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 22px; }
    .balance-summary-card { padding: 17px 19px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; }
    .balance-summary-label { margin: 0 0 5px; color: #64748b; font-size: 13px; font-weight: 600; }
    .balance-summary-value { margin: 0; color: #0f172a; font-size: 23px; font-weight: 700; }
    .balance-table-shell { min-width: 0; }
    .balance-table-shell .dataTables_wrapper { color: #475569 !important; }
    .balance-table-shell .dataTables_length,
    .balance-table-shell .dataTables_filter,
    .balance-table-shell .dataTables_info,
    .balance-table-shell .dataTables_paginate { color: #475569 !important; }
    .balance-table-shell .dataTables_filter input,
    .balance-table-shell .dataTables_length select { margin: 0 5px; padding: 6px 9px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #172033; }
    .balance-table { width: 100% !important; border-collapse: collapse !important; color: #334155; }
    .balance-table thead th { padding: 13px 12px !important; border-bottom: 1px solid #cbd5e1 !important; background: #f1f5f9; color: #334155 !important; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .balance-table tbody td { padding: 13px 12px !important; border-bottom: 1px solid #e8edf3 !important; color: #334155 !important; font-size: 13px; vertical-align: middle; }
    .balance-table tbody tr:hover { background: #f8fafc; }
    .balance-amount { display: inline-block; padding: 4px 9px; border-radius: 999px; font-weight: 700; white-space: nowrap; }
    .balance-positive { background: #dcfce7; color: #15803d; }
    .balance-negative { background: #fee2e2; color: #b91c1c; }
    .balance-neutral { background: #e2e8f0; color: #475569; }
    .balance-time { color: #64748b; white-space: nowrap; }
    .balance-note { margin: 14px 0 0; color: #64748b; font-size: 13px; }
    .balance-table-shell .dataTables_paginate .paginate_button,
    .balance-table-shell .dataTables_paginate .paginate_button.disabled { color: #475569 !important; border: 1px solid transparent !important; background: transparent !important; }
    .balance-table-shell .dataTables_paginate .paginate_button.current,
    .balance-table-shell .dataTables_paginate .paginate_button.current:hover { border-color: #2563eb !important; border-radius: 6px; background: #2563eb !important; color: #fff !important; }
    @media (max-width: 767px) {
        .balance-page { padding: 18px 10px 30px; }
        .balance-content { padding: 18px 14px; }
        .balance-title { font-size: 21px; }
    }
    @media (max-width: 480px) {
        .balance-summary { grid-template-columns: 1fr; gap: 10px; }
        .balance-summary-card { padding: 14px 16px; }
    }
</style>

<div class="balance-page">
    <div class="balance-layout grid grid-cols-8 gap-4">
        <?php require_once(__DIR__ . "/Sidebar.php"); ?>
        <div class="col-span-8 sm:col-span-5 md:col-span-6 lg:col-span-6 xl:col-span-6 balance-content">
            <header>
                <h1 class="balance-title">Biến động số dư</h1>
                <p class="balance-subtitle">Theo dõi các khoản cộng, trừ và số dư ví theo từng giao dịch.</p>
            </header>

            <div class="balance-summary">
                <div class="balance-summary-card">
                    <p class="balance-summary-label">Số dư hiện tại</p>
                    <p class="balance-summary-value"><?=format_cash($getUser['money']);?>đ</p>
                </div>
                <div class="balance-summary-card">
                    <p class="balance-summary-label">Tổng số giao dịch</p>
                    <p class="balance-summary-value"><?=number_format(count($transactions));?></p>
                </div>
            </div>

            <div class="balance-table-shell">
                <table id="datatable" class="balance-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Số tiền trước</th>
                            <th>Số tiền thay đổi</th>
                            <th>Số tiền hiện tại</th>
                            <th>Thời gian</th>
                            <th>Nội dung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $index => $row) {
                            $change = (float) $row['sotienthaydoi'];
                            $amountClass = $change > 0 ? 'balance-positive' : ($change < 0 ? 'balance-negative' : 'balance-neutral');
                            $changeText = ($change > 0 ? '+' : '') . format_cash($row['sotienthaydoi']);
                        ?>
                            <tr>
                                <td><?=number_format($index + 1);?></td>
                                <td><?=format_cash($row['sotientruoc']);?>đ</td>
                                <td><span class="balance-amount <?=$amountClass;?>"><?=$changeText;?>đ</span></td>
                                <td><strong><?=format_cash($row['sotiensau']);?>đ</strong></td>
                                <td class="balance-time"><?=htmlspecialchars((string) $row['thoigian'], ENT_QUOTES, 'UTF-8');?></td>
                                <td><?=htmlspecialchars((string) $row['noidung'], ENT_QUOTES, 'UTF-8');?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <p class="balance-note">Dùng ô tìm kiếm để lọc giao dịch; trên điện thoại có thể vuốt ngang bảng để xem thêm cột.</p>
        </div>
    </div>
</div>
<script>
$(function () {
    $('#datatable').DataTable({
        order: [],
        scrollX: true,
        pageLength: 10,
        language: {
            search: 'Tìm kiếm:',
            lengthMenu: 'Hiển thị _MENU_ giao dịch',
            info: 'Hiển thị _START_–_END_ trong _TOTAL_ giao dịch',
            infoEmpty: 'Chưa có giao dịch nào',
            infoFiltered: '(lọc từ _MAX_ giao dịch)',
            zeroRecords: 'Không tìm thấy giao dịch phù hợp',
            emptyTable: 'Chưa có biến động số dư',
            paginate: { first: 'Đầu', last: 'Cuối', next: 'Sau', previous: 'Trước' }
        }
    });
});
</script>
<?php require_once(__DIR__ . "/Footer.php"); ?>
