ALTER TABLE `product_price`  ADD COLUMN `core_id` BIGINT NULL DEFAULT NULL COMMENT 'ид записи в Core' AFTER `avg_price`;

ALTER TABLE `product_category`  ADD COLUMN `core_lft` BIGINT(20) NULL DEFAULT NULL COMMENT 'lft записи в Core' AFTER `core_parent_id`,  ADD COLUMN `core_rgt` BIGINT(20) NULL DEFAULT NULL COMMENT 'rgt записи в Core' AFTER `core_lft`;

ALTER TABLE `task`  ADD COLUMN `core_packet_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'ид пакета в Core' AFTER `core_id`;

ALTER TABLE `product_property_relation`  ADD COLUMN `value_boolean` TINYINT NULL DEFAULT NULL AFTER `value_text`;