ALTER TABLE product_category 
add `seo_title` VARCHAR(255) COMMENT 'SEO. Заголовок страницы',
add `seo_keywords` VARCHAR(255) COMMENT 'SEO. Ключевые слова',
add `seo_description` VARCHAR(255) COMMENT 'SEO. Описание',
add `seo_text` VARCHAR(255) COMMENT 'SEO текст. Под фильтром категорий';


