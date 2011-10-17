INSERT INTO `delivery_type` (`core_id`, `token`, `name`) VALUES (3, 'self', 'Самовывоз');

ALTER TABLE `page`  ADD COLUMN `has_menu` TINYINT NOT NULL DEFAULT '0' COMMENT 'Показывать меню на странице?' AFTER `content`;