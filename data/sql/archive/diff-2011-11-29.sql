ALTER TABLE `product_category`
DROP `seo_header`,
DROP `seo_title`,
DROP `seo_keywords`,
DROP `seo_description`,
DROP `seo_text`;

ALTER TABLE `product_category` 
ADD `seo_title` TEXT COMMENT 'SEO. Заголовок страницы',
ADD `seo_keywords` TEXT COMMENT 'SEO. Ключевые слова', 
ADD `seo_description` TEXT COMMENT 'SEO. Описание', 
ADD `seo_header` TEXT COMMENT 'SEO заголовок. Используется для генерации нижних breadcrumbs', 
ADD `seo_text` TEXT COMMENT 'SEO текст. Под фильтром категорий';
