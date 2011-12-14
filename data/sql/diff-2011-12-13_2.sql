ALTER TABLE `order_service_relation`
DROP FOREIGN KEY `order_service_relation_product_id_product_id`;

ALTER TABLE `order_service_relation` ADD CONSTRAINT `order_service_relation_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE SET NULL;
