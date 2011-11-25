DROP TABLE IF EXISTS `service_price`;
CREATE TABLE `service_price` (`id` BIGINT AUTO_INCREMENT, `service_price_list_id` BIGINT NOT NULL, `service_id` BIGINT, `price` DECIMAL(12, 2) DEFAULT 0 NOT NULL COMMENT 'Цена услуги', `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `service_price_list_id_idx` (`service_price_list_id`), PRIMARY KEY(`id`, `service_id`)) COMMENT = 'Стоимость услуг F1 по регионам' ENGINE = INNODB;

ALTER TABLE `service` ADD `main_photo` VARCHAR(255) AFTER work;

ALTER TABLE `service_category` ADD `main_photo` VARCHAR(255) AFTER description;