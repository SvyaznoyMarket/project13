ALTER TABLE product_category add `seo_header` VARCHAR(255) COMMENT 'SEO заголовок. Используется для генерации нижних breadcrumbs';
ALTER TABLE `product_filter`  ADD COLUMN `core_id` INT NULL DEFAULT NULL AFTER `id`;

