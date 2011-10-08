CREATE TABLE `slot` (`id` BIGINT AUTO_INCREMENT, `type` VARCHAR(20) NOT NULL COMMENT 'тип, или сущность', `token` VARCHAR(255) NOT NULL UNIQUE, `name` VARCHAR(255) COMMENT 'название', `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, PRIMARY KEY(`id`)) COMMENT = 'Слот' DEFAULT CHARACTER SET UTF8 ENGINE = INNODB;
CREATE TABLE `banner` (`id` BIGINT AUTO_INCREMENT, `slot_id` BIGINT NOT NULL, `token` VARCHAR(255) NOT NULL UNIQUE, `name` VARCHAR(255) COMMENT 'название', `link` VARCHAR(255) COMMENT 'ссылка', `image` VARCHAR(255) COMMENT 'оригинальная картинка', `image_preview` VARCHAR(255) COMMENT 'уменьшенная картинка', `position` INT DEFAULT 1 NOT NULL COMMENT 'Порядок сортировки', `created_at` DATETIME NOT NULL, `updated_at` DATETIME NOT NULL, INDEX `slot_id_idx` (`slot_id`), PRIMARY KEY(`id`)) COMMENT = 'Баннер' DEFAULT CHARACTER SET UTF8 ENGINE = INNODB;
ALTER TABLE `banner` ADD CONSTRAINT `banner_slot_id_slot_id` FOREIGN KEY (`slot_id`) REFERENCES `slot`(`id`) ON DELETE CASCADE;


INSERT INTO `slot` (`id`, `type`, `token`, `name`, `created_at`, `updated_at`) VALUES (1, 'banner', 'banner_default', 'Баннер на главной странице', '2011-10-08 11:51:58', '2011-10-08 11:51:58');


INSERT INTO `banner` (`id`, `slot_id`, `token`, `name`, `link`, `image`, `image_preview`, `position`, `created_at`, `updated_at`) VALUES (1, 1, '4e90011eb29eb', 'Баннер 1', '/', 'banner.jpg', NULL, 1, '2011-10-08 11:51:58', '2011-10-08 11:51:58');
INSERT INTO `banner` (`id`, `slot_id`, `token`, `name`, `link`, `image`, `image_preview`, `position`, `created_at`, `updated_at`) VALUES (2, 1, '4e90011eb2fa9', 'Баннер 2', '/', NULL, 'banner2.png', 1, '2011-10-08 11:51:58', '2011-10-08 11:51:58');
INSERT INTO `banner` (`id`, `slot_id`, `token`, `name`, `link`, `image`, `image_preview`, `position`, `created_at`, `updated_at`) VALUES (3, 1, '4e90011eb3535', 'Баннер 3', '/', NULL, 'banner3.png', 1, '2011-10-08 11:51:58', '2011-10-08 11:51:58');
INSERT INTO `banner` (`id`, `slot_id`, `token`, `name`, `link`, `image`, `image_preview`, `position`, `created_at`, `updated_at`) VALUES (4, 1, '4e90011eb3ab8', 'Баннер 4', '/', NULL, 'banner4.png', 1, '2011-10-08 11:51:58', '2011-10-08 11:51:58');
