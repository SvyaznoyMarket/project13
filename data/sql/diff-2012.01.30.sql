ALTER TABLE `region` ADD COLUMN `is_active` TINYINT(1) NOT NULL  AFTER `is_default` ;
UPDATE `region` SET `is_active` = 1 WHERE `type` != 'blocked' and `id` > 0;