ALTER TABLE `product_category`
	ADD COLUMN `product_view` VARCHAR(10) NOT NULL DEFAULT 'compact' COMMENT 'Вид отображения товаров в списке' AFTER `updated_at`;
