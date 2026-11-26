-- Run against the configured shopvip database. Safe to run more than once.
INSERT INTO `ecom_categories` (`title`, `slug`, `image`, `sort_order`, `status`, `created_at`)
SELECT seed.`title`, seed.`slug`, '', seed.`sort_order`, 'active', NOW()
FROM (
    SELECT 'ASUS' AS `title`, 'laptop-asus' AS `slug`, 1 AS `sort_order`
    UNION ALL SELECT 'HP', 'laptop-hp', 2
    UNION ALL SELECT 'Dell', 'laptop-dell', 3
    UNION ALL SELECT 'MacBook', 'laptop-macbook', 4
    UNION ALL SELECT 'Lenovo', 'laptop-lenovo', 5
    UNION ALL SELECT 'Acer', 'laptop-acer', 6
    UNION ALL SELECT 'MSI', 'laptop-msi', 7
    UNION ALL SELECT 'Gigabyte', 'laptop-gigabyte', 8
) AS seed
WHERE NOT EXISTS (
    SELECT 1
    FROM `ecom_categories` AS existing
    WHERE existing.`title` = seed.`title` OR existing.`slug` = seed.`slug`
);

UPDATE `ecom_categories`
SET `status` = 'active',
    `sort_order` = CASE `title`
        WHEN 'ASUS' THEN 1
        WHEN 'HP' THEN 2
        WHEN 'Dell' THEN 3
        WHEN 'MacBook' THEN 4
        WHEN 'Lenovo' THEN 5
        WHEN 'Acer' THEN 6
        WHEN 'MSI' THEN 7
        WHEN 'Gigabyte' THEN 8
        ELSE `sort_order`
    END
WHERE `title` IN ('ASUS', 'HP', 'Dell', 'MacBook', 'Lenovo', 'Acer', 'MSI', 'Gigabyte');

UPDATE `ecom_categories`
SET `status` = 'hidden'
WHERE `title` = 'TV';
