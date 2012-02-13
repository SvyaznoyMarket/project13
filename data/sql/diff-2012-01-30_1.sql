ALTER TABLE `product_delivery_price`
ADD INDEX `product_id_price_list_id` (`product_id`, `price_list_id`);