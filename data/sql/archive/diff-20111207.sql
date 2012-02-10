ALTER TABLE `product`  ADD COLUMN `token_prefix` VARCHAR(255) NULL DEFAULT NULL AFTER `token`;
ALTER TABLE `product`  DROP INDEX `token`,  ADD UNIQUE INDEX `token` (`token`, `token_prefix`);
ALTER TABLE `product_category`  ADD COLUMN `token_prefix` VARCHAR(255) NULL DEFAULT NULL AFTER `token`;
ALTER TABLE `product_category`  DROP INDEX `token`,  ADD UNIQUE INDEX `token` (`token`, `token_prefix`);