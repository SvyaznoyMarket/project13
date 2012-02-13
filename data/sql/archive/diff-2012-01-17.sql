ALTER TABLE service_price 
ADD `product_id` BIGINT COMMENT 'Привязка к продукту (если есть)' AFTER service_id,
ADD INDEX `product_id_idx` (`product_id`);
ALTER TABLE `service_price` ADD CONSTRAINT `service_price_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE;

DROP TABLE IF EXISTS service_tariff;