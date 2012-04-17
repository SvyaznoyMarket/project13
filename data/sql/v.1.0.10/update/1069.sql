ALTER TABLE `order` ADD COLUMN `sclub_card_number` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Номер карточки Связной клуб'  AFTER `delivery_price` ;
