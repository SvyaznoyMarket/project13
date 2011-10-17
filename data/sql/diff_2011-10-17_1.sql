CREATE TABLE order_status (id BIGINT AUTO_INCREMENT, token VARCHAR(255) NOT NULL UNIQUE, name VARCHAR(255) NOT NULL, position INT DEFAULT 1 NOT NULL COMMENT 'Порядок сортировки', is_order TINYINT(1) DEFAULT '0' NOT NULL COMMENT 'Этот статус предназначен для заказа?', is_product TINYINT(1) DEFAULT '0' NOT NULL COMMENT 'Этот статус предназначен для продукта в заказе?', PRIMARY KEY(id)) COMMENT = 'Статус заказа' ENGINE = INNODB;

ALTER TABLE `order`  ADD COLUMN `status_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'Статус состояния заказа' AFTER `store_id`;
ALTER TABLE `order_product_relation`  ADD COLUMN `status_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'Статус состояния товара в заказе' AFTER `price`;

ALTER TABLE order ADD CONSTRAINT order_status_id_order_status_id FOREIGN KEY (status_id) REFERENCES order_status(id) ON DELETE SET NULL;
ALTER TABLE order_product_relation ADD CONSTRAINT order_product_relation_status_id_order_status_id FOREIGN KEY (status_id) REFERENCES order_status(id) ON DELETE SET NULL;


ALTER TABLE `product_category_product_relation` ADD COLUMN `is_default` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Это основная категория товара?' AFTER `product_id`
