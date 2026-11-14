<?php
require_once __DIR__ . '/../../config/config.php';

// Truy vấn lấy thông tin từ bảng options
$links = [];
$result = $AZCODE->query("SELECT name, value FROM options WHERE name IN ('zalo', 'telegram', 'facebook')");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $links[$row['name']] = $row['value'];
    }
}
?>


<style>
    .chat-button-container {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 1100;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .chat-button {
        display: inline-flex;
        min-height: 48px;
        align-items: center;
        gap: 9px;
        padding: 0 19px;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 999px;
        background: linear-gradient(135deg, #1488d4, #1765c0);
        color: #fff;
        box-shadow: 0 12px 28px rgba(12, 87, 160, .35);
        cursor: pointer;
        font-weight: 750;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .chat-button:hover { transform: translateY(-2px); box-shadow: 0 16px 32px rgba(12, 87, 160, .44); }
    .contact-options {
        display: none;
        min-width: 190px;
        flex-direction: column;
        margin-bottom: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .2);
        opacity: 0;
        transform: translateY(8px) scale(.98);
        transition: opacity .2s ease, transform .2s ease;
    }
    .contact-options.show { display: flex; opacity: 1; transform: translateY(0) scale(1); }
    .contact-options a {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        border-bottom: 1px solid #edf1f5;
        color: #24364b;
        font-size: .9rem;
        font-weight: 650;
        text-decoration: none;
        transition: background .18s ease;
    }
    .contact-options a:hover { background: #f3f8fd; }
    .contact-options a:last-child { border-bottom: 0; }
    .contact-options img { width: 22px; height: 22px; margin-right: 10px; object-fit: contain; }
    .site-footer {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        margin-top: auto;
        padding: 34px 18px 0;
        color: #d7e4f2;
    }
    .site-footer::before {
        position: absolute;
        z-index: -1;
        top: 0;
        right: 0;
        left: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(125, 211, 252, .65), transparent);
        content: "";
    }
    .site-footer-main {
        width: min(1180px, 100%);
        margin: 0 auto;
        padding: 34px 30px;
        border: 1px solid rgba(148, 190, 225, .14);
        border-bottom: 0;
        border-radius: 22px 22px 0 0;
        background: linear-gradient(135deg, rgba(14, 30, 50, .98), rgba(13, 43, 61, .97));
        box-shadow: 0 -12px 36px rgba(0, 0, 0, .14);
    }
    .site-footer-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(150px, .7fr) minmax(190px, .8fr);
        gap: 34px;
    }
    .site-footer-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .site-footer-brand img { width: auto; max-width: 125px; max-height: 42px; object-fit: contain; }
    .site-footer h2, .site-footer h3 { margin: 0; color: #fff; font-weight: 800; }
    .site-footer h2 { font-size: 1.08rem; letter-spacing: .02em; }
    .site-footer h3 { margin-bottom: 14px; font-size: .9rem; }
    .site-footer p { max-width: 460px; margin: 0; color: #aabbd0; font-size: .9rem; line-height: 1.75; }
    .site-footer-links, .site-footer-socials { display: grid; gap: 10px; }
    .site-footer-links a, .site-footer-socials a {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 9px;
        color: #c7d7e7;
        font-size: .88rem;
        text-decoration: none;
        transition: color .18s ease, transform .18s ease;
    }
    .site-footer-links a:hover, .site-footer-socials a:hover { transform: translateX(3px); color: #7dd3fc; }
    .site-footer-socials img { width: 22px; height: 22px; object-fit: contain; }
    .site-footer-bottom {
        display: flex;
        width: min(1180px, 100%);
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 auto;
        padding: 17px 4px 20px;
        border-top: 1px solid rgba(148, 190, 225, .13);
        color: #8fa3b9;
        font-size: .82rem;
    }
    .site-footer-bottom a { color: #d9e7f5; font-weight: 700; text-decoration: none; }
    .site-footer-bottom a:hover { color: #7dd3fc; }
    @media (max-width: 760px) {
        .site-footer { padding: 24px 12px 0; }
        .site-footer-main { padding: 26px 20px; border-radius: 18px 18px 0 0; }
        .site-footer-grid { grid-template-columns: 1fr 1fr; gap: 25px 20px; }
        .site-footer-brand { grid-column: 1 / -1; }
        .site-footer-bottom { align-items: flex-start; flex-direction: column; padding: 15px 3px 20px; }
        .chat-button-container { right: 14px; bottom: 14px; }
    }
    @media (max-width: 480px) {
        .site-footer-grid { grid-template-columns: 1fr; }
        .site-footer-brand { grid-column: auto; }
        .chat-button { min-height: 44px; padding: 0 15px; }
    }
</style>

<div class="chat-button-container">
    <div id="contactOptions" class="contact-options">
        <?php if (!empty($links['zalo'])): ?>
            <a href="<?= htmlspecialchars($links['zalo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                <img src="https://i.imgur.com/CK0CsCn.png" alt="Zalo"> Chat Zalo
            </a>
        <?php endif; ?>
        
        <?php if (!empty($links['telegram'])): ?>
            <a href="<?= htmlspecialchars($links['telegram'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                <img src="https://i.imgur.com/RAa05dH.png" alt="Telegram"> Chat Telegram
            </a>
        <?php endif; ?>
        
        <?php if (!empty($links['facebook'])): ?>
            <a href="<?= htmlspecialchars($links['facebook'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                <img src="https://i.imgur.com/euTdsUd.png" alt="Facebook"> Chat Facebook
            </a>
        <?php endif; ?>
    </div>
    <div class="chat-button" onclick="toggleOptions()">
        <i class="fas fa-comments"></i> Hỗ Trợ
    </div>
</div>

<script>
    function toggleOptions() {
        const options = document.getElementById('contactOptions');
        options.classList.toggle('show');
    }
</script>

<div id="thongbao"></div>
<script src="<?=BASE_URL('template/theme/');?>assets/frontend/js/footer.js"></script>
<script src="<?=BASE_URL('live_chat.js');?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<script>
new ClipboardJS('.copy');
</script>
<footer class="site-footer">
    <div class="site-footer-main">
        <div class="site-footer-grid">
            <section>
                <div class="site-footer-brand">
                    <?php if ($AZCODE->site('logo')) { ?>
                        <img src="<?=htmlspecialchars((string) $AZCODE->site('logo'), ENT_QUOTES, 'UTF-8');?>"
                            alt="<?=htmlspecialchars((string) $AZCODE->site('tenweb'), ENT_QUOTES, 'UTF-8');?>">
                    <?php } else { ?>
                        <h2><?=htmlspecialchars((string) ($AZCODE->site('tenweb') ?: 'Cửa hàng công nghệ'), ENT_QUOTES, 'UTF-8');?></h2>
                    <?php } ?>
                </div>
                <p>Cửa hàng công nghệ và điện tử. Thông tin sản phẩm, thanh toán, bảo hành và hỗ trợ khách hàng được trình bày rõ ràng, thuận tiện.</p>
            </section>
            <section>
                <h3>Khám phá</h3>
                <div class="site-footer-links">
                    <a href="<?=BASE_URL('Shop');?>"><i class="bx bx-store"></i> Cửa hàng</a>
                    <a href="<?=BASE_URL('Cart');?>"><i class="bx bx-cart"></i> Giỏ hàng</a>
                    <a href="<?=BASE_URL('Auth/Login');?>"><i class="bx bx-user"></i> Tài khoản</a>
                </div>
            </section>
            <section>
                <h3>Kết nối với chúng tôi</h3>
                <div class="site-footer-socials">
                    <?php if (!empty($links['zalo'])): ?>
                        <a href="<?=htmlspecialchars($links['zalo'], ENT_QUOTES, 'UTF-8');?>" target="_blank" rel="noopener noreferrer">
                            <img src="https://i.imgur.com/CK0CsCn.png" alt=""> Zalo
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($links['telegram'])): ?>
                        <a href="<?=htmlspecialchars($links['telegram'], ENT_QUOTES, 'UTF-8');?>" target="_blank" rel="noopener noreferrer">
                            <img src="https://i.imgur.com/RAa05dH.png" alt=""> Telegram
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($links['facebook'])): ?>
                        <a href="<?=htmlspecialchars($links['facebook'], ENT_QUOTES, 'UTF-8');?>" target="_blank" rel="noopener noreferrer">
                            <img src="https://i.imgur.com/euTdsUd.png" alt=""> Facebook
                        </a>
                    <?php endif; ?>
                    <?php if (empty($links['zalo']) && empty($links['telegram']) && empty($links['facebook'])) { ?>
                        <p>Liên hệ cửa hàng để được tư vấn và hỗ trợ.</p>
                    <?php } ?>
                </div>
            </section>
        </div>
    </div>
    <div class="site-footer-bottom">
        <span>&copy; <?=date('Y');?> <?=htmlspecialchars((string) ($AZCODE->site('tenweb') ?: 'Cửa hàng'), ENT_QUOTES, 'UTF-8');?>. Bảo lưu mọi quyền.</span>
        <span>Thiết kế bởi <a href="https://azcode.vn/" target="_blank" rel="noopener noreferrer">AZCODE.VN</a></span>
    </div>
</footer>
</div>
<button type="button" class="cd-top h-10 w-10 border-2 border-blue-600 fixed opacity-90 rounded text-2xl text-white bg-blue-600 rounded-full font-bold flex items-center justify-center focus:outline-none" style="right:2%;bottom:14%;">
    <i class="bx bx-up-arrow-alt"></i>
</button>
</body>
</html>
