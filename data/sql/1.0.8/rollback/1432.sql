ALTER TABLE `banner` ADD COLUMN `is_dummy` TINYINT(1) DEFAULT '0' NOT NULL AFTER `is_active`;
UPDATE `banner` SET `is_dummy` = FIELD(`type`, 'banner', 'dummy') - 1;
ALTER TABLE `banner` DROP COLUMN `type`;