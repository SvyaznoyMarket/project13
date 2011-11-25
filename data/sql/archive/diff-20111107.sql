ALTER TABLE `product_filter`  ADD COLUMN `value_min` BIGINT(20) NULL DEFAULT NULL AFTER `position`,  ADD COLUMN `value_max` BIGINT(20) NULL DEFAULT NULL AFTER `value_min`;

UPDATE product_filter pf
INNER JOIN product_property pp ON pp.id = pf.property_id
SET pf.type = 'checkbox'
WHERE pp.`type` = 'boolean'