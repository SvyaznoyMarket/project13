ALTER TABLE `order` ADD COLUMN `number` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Номер заказа из 1С' AFTER `delivery_period_id`;
ALTER TABLE `order` ADD COLUMN `core_created_at` DATETIME NOT NULL COMMENT 'Дата создания заказа (по ядру)' AFTER `step`;
ALTER TABLE `product` ADD COLUMN `is_lines_main` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Главный товар в линии?' AFTER `is_instock`;
ALTER TABLE `product_category` ADD COLUMN `had_line` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Есть серии для отображения' AFTER `is_active`;