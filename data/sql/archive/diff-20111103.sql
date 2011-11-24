ALTER TABLE `task`  ADD COLUMN `step` INT(4) NOT NULL DEFAULT '0' COMMENT 'Количество запусков' AFTER `attempt`;
