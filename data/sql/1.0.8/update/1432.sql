ALTER TABLE `banner` ADD COLUMN `type` VARCHAR(20) NOT NULL AFTER `is_active`;
UPDATE `banner` SET `type` = IF(`is_dummy` = 1, 'dummy', 'banner');
ALTER TABLE `banner` DROP COLUMN `is_dummy`;