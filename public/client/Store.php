<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/ecommerce.php");

ecom_capture_referrer();
$title = 'CỬA HÀNG | ' . $AZCODE->site('tenweb');
require_once(__DIR__ . "/Header.php");
require_once(__DIR__ . "/Nav.php");

$installed = ecom_is_installed();
if ($installed) {
    ecom_expire_unpaid_orders();
}
$search = trim((string) ($_GET['q'] ?? ''));
$categoryId = filter_var($_GET['category'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$bannerImages = array_values(array_filter(
    array_map('trim', preg_split('/\R/', (string) $AZCODE->site('home_banners'))),
    static function ($url) {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        return $url !== '' && ($scheme === null || in_array(strtolower($scheme), ['http', 'https'], true));
    }
));
$products = [];
$categories = [];

if ($installed) {
    $categories = $AZCODE->get_list(
        "SELECT `id`,`title` FROM `ecom_categories` WHERE `status` = 'active' ORDER BY `sort_order`,`title`"
    );
    $where = ["p.`approval_status` = 'approved'", "p.`stock` > 0"];
    if ($search !== '') {
        $term = $AZCODE->escape($search);
        $where[] = "(p.`name` LIKE '%$term%' OR p.`brand` LIKE '%$term%' OR p.`model` LIKE '%$term%')";
    }
    if ($categoryId !== false) {
        $where[] = "p.`category_id` = '" . (int) $categoryId . "'";
    }
    $perPage = 12;
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $countRow = $AZCODE->get_row(
        "SELECT COUNT(*) AS total FROM `ecom_products` p"
        . " INNER JOIN `ecom_categories` c ON c.`id` = p.`category_id` AND c.`status` = 'active'"
        . " WHERE " . implode(' AND ', $where)
    );
    $total = (int) ($countRow['total'] ?? 0);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    $products = $AZCODE->get_list(
        "SELECT p.*, c.`title` AS `category_title` FROM `ecom_products` p"
        . " INNER JOIN `ecom_categories` c ON c.`id` = p.`category_id` AND c.`status` = 'active'"
        . " WHERE " . implode(' AND ', $where)
        . " ORDER BY p.`created_at` DESC LIMIT $offset,$perPage"
    );
}
?>
<style>
    .storefront-page {
        --store-ink: #12213a;
        --store-muted: #64748b;
        min-height: 75vh;
        padding: clamp(24px, 4vw, 54px) 16px 64px;
        color: var(--store-ink);
        background:
            radial-gradient(ellipse at 8% 8%, rgba(147, 197, 253, .38), transparent 34%),
            radial-gradient(ellipse at 94% 24%, rgba(165, 243, 252, .42), transparent 30%),
            linear-gradient(180deg, #f8fbff 0%, #edf4fb 100%);
    }
    .storefront-container { width: min(1180px, 100%); margin: 0 auto; }
    .store-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, .7fr);
        align-items: center;
        gap: 24px;
        min-height: 270px;
        padding: clamp(28px, 5vw, 58px);
        color: #fff;
        border-radius: 28px;
        background: linear-gradient(118deg, #10213c 0%, #173d70 58%, #087e91 100%);
        box-shadow: 0 24px 55px rgba(15, 42, 75, .2);
    }
    .store-hero::before, .store-hero::after {
        content: "";
        position: absolute;
        z-index: -1;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 50%;
    }
    .store-hero::before { width: 390px; height: 390px; right: 2%; top: -55%; }
    .store-hero::after { width: 290px; height: 290px; right: 14%; bottom: -62%; }
    .store-hero-copy { max-width: 690px; }
    .store-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 14px;
        color: #a5f3fc;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .store-hero h1 {
        margin: 0;
        color: #fff;
        font-size: clamp(2rem, 4vw, 3.2rem);
        font-weight: 850;
        line-height: 1.08;
        letter-spacing: -.035em;
    }
    .store-hero-copy > p:not(.store-hero-kicker) {
        max-width: 610px;
        margin: 16px 0 0;
        color: #d7e8f7;
        font-size: 1rem;
        line-height: 1.7;
    }
    .store-hero-highlights { display: flex; flex-wrap: wrap; gap: 10px 20px; margin-top: 23px; }
    .store-hero-highlights span { color: #e4f6ff; font-size: .88rem; font-weight: 650; }
    .store-hero-art {
        display: flex;
        min-height: 160px;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,.92);
        font-size: clamp(1.05rem, 2vw, 1.4rem);
        font-weight: 800;
        letter-spacing: .12em;
        text-align: center;
        text-shadow: 0 8px 26px rgba(0,0,0,.25);
    }
    .store-hero-art span {
        display: grid;
        width: 190px;
        height: 190px;
        place-items: center;
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 42px;
        background: linear-gradient(145deg, rgba(255,255,255,.16), rgba(255,255,255,.04));
        box-shadow: inset 0 1px rgba(255,255,255,.18), 0 22px 50px rgba(0,0,0,.16);
        transform: rotate(7deg);
    }
    .store-banner {
        position: relative;
        overflow: hidden;
        aspect-ratio: 3.15 / 1;
        margin-bottom: 24px;
        border-radius: 20px;
        background: #dbe7f2;
        box-shadow: 0 16px 36px rgba(15, 42, 75, .12);
    }
    .store-banner-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        pointer-events: none;
        transition: opacity .7s ease;
    }
    .store-banner-slide.is-active { z-index: 1; opacity: 1; pointer-events: auto; }
    .store-banner-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .store-banner-arrow {
        position: absolute;
        top: 50%;
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 0;
        border-radius: 50%;
        background: rgba(15, 32, 54, .62);
        color: #fff;
        font-size: 1.4rem;
        cursor: pointer;
        transform: translateY(-50%);
    }
    .store-banner-arrow:hover { background: rgba(15, 32, 54, .86); }
    .store-banner-prev { left: 14px; }
    .store-banner-next { right: 14px; }
    .store-banner-dots {
        position: absolute;
        right: 0;
        bottom: 12px;
        left: 0;
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    .store-banner-dot {
        width: 9px;
        height: 9px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: rgba(255,255,255,.62);
        box-shadow: 0 1px 5px rgba(0,0,0,.24);
        cursor: pointer;
    }
    .store-banner-dot.is-active { width: 23px; border-radius: 999px; background: #fff; }
    .store-filter-panel {
        display: grid;
        grid-template-columns: minmax(200px, 1fr) minmax(170px, 230px) auto auto;
        gap: 10px;
        align-items: center;
        margin: 28px 0 30px;
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: rgba(255,255,255,.92);
        box-shadow: 0 12px 30px rgba(15, 42, 75, .07);
    }
    .store-filter-panel input, .store-filter-panel select {
        width: 100%;
        min-height: 46px;
        padding: 0 14px;
        border: 1px solid #dbe4ef;
        border-radius: 11px;
        outline: none;
        background: #f8fafc;
        color: #172b4d;
        font-size: .95rem;
    }
    .store-filter-panel input:focus, .store-filter-panel select:focus {
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49,130,206,.13);
    }
    .store-filter-button, .store-cart-link {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 18px;
        border: 0;
        border-radius: 11px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .store-filter-button { background: #1677d2; color: #fff; cursor: pointer; }
    .store-cart-link { border: 1px solid #cbd9e8; background: #fff; color: #173d70; }
    .store-filter-button:hover, .store-cart-link:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(23,61,112,.15); }
    .store-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin: 0 2px 17px;
    }
    .store-section-heading h2 { margin: 0; color: #12213a; font-size: 1.45rem; font-weight: 800; }
    .store-section-heading p { margin: 5px 0 0; color: var(--store-muted); font-size: .92rem; }
    .store-section-count { color: #52657e; font-size: .9rem; font-weight: 700; }
    .store-product-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }
    .store-product-card {
        display: flex;
        min-width: 0;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 42, 75, .06);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .store-product-card:hover { transform: translateY(-5px); border-color: #bfd7ef; box-shadow: 0 18px 34px rgba(15, 42, 75, .13); }
    .store-product-visual {
        position: relative;
        display: block;
        aspect-ratio: 1.18 / 1;
        overflow: hidden;
        padding: 14px;
        background: linear-gradient(145deg, #f1f7fc 0%, #e8f1fa 100%);
    }
    .store-product-visual img { width: 100%; height: 100%; object-fit: contain; transition: transform .25s ease; }
    .store-product-card:hover .store-product-visual img { transform: scale(1.045); }
    .store-product-placeholder {
        display: grid;
        width: 100%;
        height: 100%;
        place-items: center;
        border: 1px dashed #bbcee1;
        border-radius: 12px;
        color: #66809c;
        font-size: .9rem;
        font-weight: 650;
    }
    .store-product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 6px 9px;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        color: #1768a6;
        font-size: .72rem;
        font-weight: 800;
        box-shadow: 0 3px 10px rgba(15,42,75,.08);
    }
    .store-product-content { display: flex; flex: 1; flex-direction: column; padding: 16px; }
    .store-product-category { margin: 0 0 7px; color: #1677a8; font-size: .76rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
    .store-product-name { min-height: 2.8em; margin: 0; color: #142640; font-size: 1.04rem; font-weight: 800; line-height: 1.4; }
    .store-product-name a { color: inherit; text-decoration: none; }
    .store-product-name a:hover { color: #1677d2; }
    .store-product-meta { min-height: 1.4em; margin: 6px 0 14px; color: #718198; font-size: .85rem; }
    .store-product-price { margin: auto 0 4px; color: #1263a5; font-size: 1.22rem; font-weight: 850; }
    .store-product-details { display: flex; flex-wrap: wrap; gap: 6px 12px; margin: 8px 0 15px; color: #64748b; font-size: .79rem; }
    .store-product-cta {
        display: flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        border: 1px solid #c9def1;
        border-radius: 10px;
        color: #1768a6;
        font-size: .9rem;
        font-weight: 800;
        text-decoration: none;
        transition: background .18s ease, color .18s ease;
    }
    .store-product-cta:hover { background: #1677d2; color: #fff; }
    .store-empty-state, .store-unavailable {
        padding: 44px 20px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: rgba(255,255,255,.88);
        color: #52657e;
        text-align: center;
    }
    .store-empty-state strong { display: block; margin-bottom: 7px; color: #18304d; font-size: 1.1rem; }
    @media (max-width: 900px) {
        .store-product-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .store-filter-panel { grid-template-columns: minmax(180px, 1fr) minmax(150px, 220px) auto; }
        .store-cart-link { grid-column: 1 / -1; }
    }
    @media (max-width: 680px) {
        .storefront-page { padding: 20px 12px 44px; }
        .store-banner { aspect-ratio: 1.65 / 1; margin-bottom: 16px; border-radius: 14px; }
        .store-banner-arrow { width: 34px; height: 34px; }
        .store-banner-prev { left: 9px; }
        .store-banner-next { right: 9px; }
        .store-hero { grid-template-columns: 1fr; min-height: auto; padding: 30px 24px; border-radius: 21px; }
        .store-hero-art { display: none; }
        .store-hero h1 { font-size: 2.15rem; }
        .store-filter-panel { grid-template-columns: 1fr 1fr; padding: 11px; }
        .store-filter-panel input { grid-column: 1 / -1; }
        .store-filter-button, .store-cart-link { width: 100%; padding: 0 10px; }
        .store-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .store-product-content { padding: 12px; }
        .store-product-name { font-size: .94rem; }
        .store-product-meta { font-size: .78rem; }
        .store-product-price { font-size: 1.05rem; }
    }
    @media (max-width: 380px) {
        .store-product-grid { grid-template-columns: 1fr; }
        .store-product-visual { aspect-ratio: 1.5 / 1; }
        .store-section-heading { align-items: start; flex-direction: column; }
    }
    @media (prefers-reduced-motion: reduce) {
        .store-banner-slide { transition: none; }
    }
</style>

<main class="storefront-page">
    <div class="storefront-container">
        <?php if ($bannerImages) { ?>
            <section class="store-banner" aria-label="Banner cửa hàng" data-store-banner>
                <?php foreach ($bannerImages as $index => $bannerImage) { ?>
                    <div class="store-banner-slide<?=$index === 0 ? ' is-active' : '';?>" aria-hidden="<?=$index === 0 ? 'false' : 'true';?>">
                        <img src="<?=ecom_h($bannerImage);?>" alt="Banner cửa hàng <?=($index + 1);?>" <?=($index === 0 ? 'fetchpriority="high"' : 'loading="lazy"');?>>
                    </div>
                <?php } ?>
                <?php if (count($bannerImages) > 1) { ?>
                    <button class="store-banner-arrow store-banner-prev" type="button" aria-label="Banner trước">&#8249;</button>
                    <button class="store-banner-arrow store-banner-next" type="button" aria-label="Banner tiếp theo">&#8250;</button>
                    <div class="store-banner-dots" aria-label="Chọn banner">
                        <?php foreach ($bannerImages as $index => $bannerImage) { ?>
                            <button class="store-banner-dot<?=$index === 0 ? ' is-active' : '';?>" type="button"
                                aria-label="Hiển thị banner <?=($index + 1);?>" aria-current="<?=$index === 0 ? 'true' : 'false';?>"
                                data-banner-index="<?=$index;?>"></button>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        <?php } ?>
        <section class="store-hero">
            <div class="store-hero-copy">
                <p class="store-hero-kicker">Cửa hàng công nghệ & điện tử</p>
                <h1>Thiết bị tốt.<br>Trải nghiệm tuyệt vời.</h1>
                <p>Tìm sản phẩm phù hợp, xem rõ thông tin bảo hành và theo dõi đơn hàng ngay tại cửa hàng.</p>
                <div class="store-hero-highlights">
                    <span>✓ Thanh toán chuyển khoản</span>
                    <span>✓ Theo dõi đơn hàng</span>
                    <span>✓ Thông tin bảo hành rõ ràng</span>
                </div>
            </div>
            <div class="store-hero-art" aria-hidden="true"><span>TECH<br>FOR YOU</span></div>
        </section>

        <?php if (!$installed) { ?>
            <div class="store-unavailable">
                Cửa hàng mới chưa được khởi tạo. Quản trị viên cần sao lưu database và import
                <code>ecommerce-migration.sql</code> trước khi mở bán.
            </div>
        <?php } else { ?>
            <form method="get" action="<?=ecom_h(BASE_URL('Shop'));?>" class="store-filter-panel">
                <input name="q" value="<?=ecom_h($search);?>" placeholder="Tìm tên sản phẩm, hãng hoặc model">
                <select name="category" aria-label="Lọc theo danh mục">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?=$category['id'];?>" <?=((int) $categoryId === (int) $category['id']) ? 'selected' : '';?>>
                            <?=ecom_h($category['title']);?>
                        </option>
                    <?php } ?>
                </select>
                <button class="store-filter-button" type="submit">Tìm sản phẩm</button>
                <a class="store-cart-link" href="<?=ecom_h(BASE_URL('Cart'));?>">
                    Giỏ hàng <span>(<?=array_sum(ecom_cart());?>)</span>
                </a>
            </form>

            <div class="store-section-heading">
                <div>
                    <h2><?=($search !== '' || $categoryId !== false) ? 'Sản phẩm phù hợp' : 'Sản phẩm nổi bật';?></h2>
                    <p>Thiết bị công nghệ được tuyển chọn cho nhu cầu mỗi ngày.</p>
                </div>
                <span class="store-section-count"><?=$total;?> sản phẩm</span>
            </div>

            <?php if (!$products) { ?>
                <div class="store-empty-state">
                    <strong>Chưa tìm thấy sản phẩm phù hợp</strong>
                    Hãy thử từ khóa khác hoặc chọn tất cả danh mục để xem sản phẩm.
                </div>
            <?php } else { ?>
                <div class="store-product-grid">
                    <?php foreach ($products as $product) { ?>
                        <?php $productUrl = ecom_h(BASE_URL('Product/' . (int) $product['id'])); ?>
                        <article class="store-product-card">
                            <a class="store-product-visual" href="<?=$productUrl;?>" aria-label="Xem <?=ecom_h($product['name']);?>">
                                <?php if (!empty($product['image_url'])) { ?>
                                    <img src="<?=ecom_h($product['image_url']);?>" alt="<?=ecom_h($product['name']);?>" loading="lazy">
                                <?php } else { ?>
                                    <span class="store-product-placeholder">Hình ảnh sản phẩm</span>
                                <?php } ?>
                                <?php if ((int) $product['warranty_months'] > 0) { ?>
                                    <span class="store-product-badge">Bảo hành <?=number_format((int) $product['warranty_months']);?> tháng</span>
                                <?php } ?>
                            </a>
                            <div class="store-product-content">
                                <p class="store-product-category"><?=ecom_h($product['category_title'] ?: 'Công nghệ');?></p>
                                <h3 class="store-product-name"><a href="<?=$productUrl;?>"><?=ecom_h($product['name']);?></a></h3>
                                <p class="store-product-meta"><?=ecom_h(trim($product['brand'] . ' ' . $product['model']));?></p>
                                <p class="store-product-price"><?=number_format((float) $product['price'], 0, ',', '.');?> đ</p>
                                <div class="store-product-details">
                                    <span>Còn <?=number_format((int) $product['stock']);?> sản phẩm</span>
                                </div>
                                <a class="store-product-cta" href="<?=$productUrl;?>">Xem chi tiết <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    <?php } ?>
                </div>
                <?php
                    $paginationParams = [];
                    if ($search !== '') $paginationParams['q'] = $search;
                    if ($categoryId !== false) $paginationParams['category'] = (int) $categoryId;
                    $paginationUrl = BASE_URL('Shop');
                    if ($paginationParams) $paginationUrl .= '?' . http_build_query($paginationParams);
                    echo pagination_control($paginationUrl, $page, $total, $perPage);
                ?>
            <?php } ?>
        <?php } ?>
    </div>
</main>
<script>
    (function () {
        const carousel = document.querySelector('[data-store-banner]');
        if (!carousel) return;

        const slides = Array.from(carousel.querySelectorAll('.store-banner-slide'));
        const dots = Array.from(carousel.querySelectorAll('.store-banner-dot'));
        if (slides.length < 2) return;

        let activeIndex = 0;
        const showSlide = function (index) {
            activeIndex = (index + slides.length) % slides.length;
            slides.forEach(function (slide, slideIndex) {
                const isActive = slideIndex === activeIndex;
                slide.classList.toggle('is-active', isActive);
                slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });
            dots.forEach(function (dot, dotIndex) {
                const isActive = dotIndex === activeIndex;
                dot.classList.toggle('is-active', isActive);
                dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        };

        carousel.querySelector('.store-banner-prev').addEventListener('click', function () {
            showSlide(activeIndex - 1);
        });
        carousel.querySelector('.store-banner-next').addEventListener('click', function () {
            showSlide(activeIndex + 1);
        });
        dots.forEach(function (dot, index) {
            dot.addEventListener('click', function () {
                showSlide(index);
            });
        });

        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let timer = window.setInterval(function () {
                showSlide(activeIndex + 1);
            }, 5000);
            carousel.addEventListener('mouseenter', function () {
                window.clearInterval(timer);
            });
            carousel.addEventListener('mouseleave', function () {
                window.clearInterval(timer);
                timer = window.setInterval(function () {
                    showSlide(activeIndex + 1);
                }, 5000);
            });
        }
    })();
</script>
<?php require_once(__DIR__ . "/Footer.php"); ?>
