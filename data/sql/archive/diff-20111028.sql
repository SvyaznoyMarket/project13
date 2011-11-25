SET foreign_key_checks = 0;

DROP TABLE `stock_product_relation`;

CREATE TABLE `stock_product_relation` (`id` BIGINT AUTO_INCREMENT, `product_id` BIGINT NOT NULL, `stock_id` BIGINT, `shop_id` BIGINT, `quantity` INT DEFAULT 1 NOT NULL, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `product_id_idx` (`product_id`), INDEX `stock_id_idx` (`stock_id`), INDEX `shop_id_idx` (`shop_id`), PRIMARY KEY(`id`)) COMMENT = 'Связь склада и товара' ENGINE = INNODB;

ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_stock_id_stock_id` FOREIGN KEY (`stock_id`) REFERENCES `stock`(`id`) ON DELETE SET NULL;
ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_shop_id_shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shop`(`id`) ON DELETE SET NULL;
ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE;

SET foreign_key_checks = 1;