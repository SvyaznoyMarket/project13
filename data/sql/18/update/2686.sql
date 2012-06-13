ALTER TABLE `wp_term_taxonomy`
ADD COLUMN `priority` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Приоритет показа';