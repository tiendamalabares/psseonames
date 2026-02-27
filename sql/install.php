<?php

return [
    'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'psseonames_category` (
        `id_category` INT UNSIGNED NOT NULL,
        `id_lang` INT UNSIGNED NOT NULL,
        `id_shop` INT UNSIGNED NOT NULL,
        `seo_name` VARCHAR(255) NOT NULL DEFAULT \'\',
        PRIMARY KEY (`id_category`, `id_lang`, `id_shop`),
        KEY `idx_psseonames_category_shop` (`id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
];
