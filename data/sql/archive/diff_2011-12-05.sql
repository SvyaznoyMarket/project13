CREATE TABLE `product_delivery_price` (`id` BIGINT AUTO_INCREMENT, `product_id` BIGINT NOT NULL, `delivery_type_id` BIGINT DEFAULT 1 NOT NULL, `price` BIGINT, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `product_id_idx` (`product_id`), INDEX `delivery_type_id_idx` (`delivery_type_id`), PRIMARY KEY(`id`)) COMMENT = 'Цены на доставку продуктов' ENGINE = INNODB;

INSERT INTO product_delivery_price
SELECT
 '',id, 1, price, '', '', ''
FROM test_table;