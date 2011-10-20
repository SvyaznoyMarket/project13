ALTER TABLE `product`    ADD COLUMN `prefix` VARCHAR(255) NULL DEFAULT NULL AFTER `name`
ALTER TABLE `tag_group_product_type_relation`    ADD COLUMN `position` INT(4) NOT NULL DEFAULT '0' AFTER `product_type_id`
ALTER TABLE `product_category`    ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `photo`