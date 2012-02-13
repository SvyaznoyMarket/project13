ALTER TABLE `product_delivery_price_core`
ADD `price_list_core_id` bigint(20) NOT NULL FIRST;

ALTER TABLE `product_delivery_price` ADD `price_list_id` BIGINT DEFAULT 1 NOT NULL AFTER id;
ALTER TABLE `product_delivery_price` ADD CONSTRAINT `product_delivery_price_price_list_id_product_price_list_id` FOREIGN KEY (`price_list_id`) REFERENCES `product_price_list`(`id`) ON DELETE CASCADE;
