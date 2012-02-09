ALTER TABLE `service_category`
ADD `position` int(11) NULL DEFAULT '1' AFTER `main_photo`;

CREATE TABLE `service_tariff` (`id` BIGINT AUTO_INCREMENT, `region_id` BIGINT NOT NULL, `service_id` BIGINT NOT NULL, `product_id` BIGINT NOT NULL, `price` DECIMAL(12, 2) DEFAULT 0 NOT NULL COMMENT 'Цена услуги', `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `region_id_idx` (`region_id`), INDEX `service_id_idx` (`service_id`), INDEX `product_id_idx` (`product_id`), PRIMARY KEY(`id`)) COMMENT = 'Тарифы услуг F1 по регионам' ENGINE = INNODB;
