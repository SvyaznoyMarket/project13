CREATE TABLE `product` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `category_id` BIGINT NOT NULL, INDEX `category_id_idx` (`category_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `product_category` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `product_photo` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `product_id` BIGINT NOT NULL, INDEX `product_id_idx` (`product_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `product_property` (`id` BIGINT AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `product_id` BIGINT NOT NULL, INDEX `product_id_idx` (`product_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
ALTER TABLE `product` ADD CONSTRAINT `product_category_id_product_category_id` FOREIGN KEY (`category_id`) REFERENCES `product_category`(`id`) ON DELETE RESTRICT;
ALTER TABLE `product_photo` ADD CONSTRAINT `product_photo_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE;
ALTER TABLE `product_property` ADD CONSTRAINT `product_property_product_id_product_id` FOREIGN KEY (`product_id`) REFERENCES `product`(`id`) ON DELETE CASCADE;
