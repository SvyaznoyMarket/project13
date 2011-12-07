ALTER TABLE service_category
ADD `core_lft` BIGINT COMMENT 'lft записи в Core' AFTER core_parent_id, 
ADD `core_rgt` BIGINT COMMENT 'rgt записи в Core' AFTER core_lft;