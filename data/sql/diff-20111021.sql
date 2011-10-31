ALTER TABLE `product_price`  ADD COLUMN `core_id` BIGINT NULL DEFAULT NULL COMMENT 'ид записи в Core' AFTER `avg_price`;
