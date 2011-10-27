SET foreign_key_checks = 0;

DROP TABLE stock_product_relation;
CREATE TABLE `stock_product_relation` (`product_id` BIGINT, `stock_id` BIGINT, `shop_id` BIGINT, `quantity` INT DEFAULT 1 NOT NULL, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, PRIMARY KEY(`product_id`, `stock_id`, `shop_id`)) COMMENT = 'Связь склада и товара' ENGINE = INNODB;

ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_stock_id_stock_id` FOREIGN KEY (`stock_id`) REFERENCES `stock`(`id`);
ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_shop_id_shop_id` FOREIGN KEY (`shop_id`) REFERENCES `shop`(`id`);
ALTER TABLE `stock_product_relation` ADD CONSTRAINT `stock_product_relation_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE;

SET foreign_key_checks = 1;

