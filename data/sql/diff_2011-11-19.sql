ALTER TABLE `tag_group_product_category_relation` ADD COLUMN `position` INT NOT NULL DEFAULT '1' COMMENT 'Порядок сортировки' AFTER `product_category_id`;
ALTER TABLE `tag_group_product_category_relation` ADD COLUMN `core_id` BIGINT NULL DEFAULT NULL COMMENT 'ид записи в Core' AFTER `product_category_id`;
