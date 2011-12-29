DROP TABLE `banner`;

CREATE TABLE `banner` (`id` BIGINT AUTO_INCREMENT, `slot_id` BIGINT, `token` VARCHAR(255) NOT NULL UNIQUE, `name` VARCHAR(255) COMMENT 'название', `link` VARCHAR(255) COMMENT 'ссылка', `image` VARCHAR(255) COMMENT 'оригинальная картинка', `position` INT DEFAULT 1 NOT NULL COMMENT 'Порядок сортировки', `is_active` TINYINT(1) DEFAULT '0' NOT NULL, `is_dummy` TINYINT(1) DEFAULT '0' NOT NULL, `start_at` DATETIME, `end_at` DATETIME, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `slot_id_idx` (`slot_id`), PRIMARY KEY(`id`)) COMMENT = 'Баннер' ENGINE = INNODB;
CREATE TABLE `banner_item` (`id` BIGINT AUTO_INCREMENT, `banner_id` BIGINT NOT NULL, `type` VARCHAR(20) NOT NULL, `object_id` BIGINT, `timeout` INT, `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, `core_id` BIGINT COMMENT 'ид записи в Core', INDEX `banner_id_idx` (`banner_id`), PRIMARY KEY(`id`)) COMMENT = 'Элемент баннера' ENGINE = INNODB;
ALTER TABLE `banner` ADD CONSTRAINT `banner_slot_id_slot_id` FOREIGN KEY (`slot_id`) REFERENCES `slot`(`id`) ON DELETE SET NULL;
ALTER TABLE `banner_item` ADD CONSTRAINT `banner_item_banner_id_banner_id` FOREIGN KEY (`banner_id`) REFERENCES `banner`(`id`) ON DELETE CASCADE;

ALTER TABLE `banner`  ADD COLUMN `timeout` INT(4) NULL DEFAULT NULL AFTER `start_at`;
ALTER TABLE `banner_item`  DROP COLUMN `timeout`;
ALTER TABLE `banner_item`  ADD COLUMN `position` INT(4) NOT NULL DEFAULT '1' AFTER `updated_at`;