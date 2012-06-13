ALTER TABLE `wp_term_taxonomy`
DROP COLUMN `priority`;

DELETE FROM `wp_terms` WHERE `term_id` IN (15, 16, 17, 18, 19, 21, 22, 23, 24, 25, 26, 28, 29, 30);

DELETE FROM `wp_term_relationships` WHERE `object_id` IN (
  335, 299, 336, 338, 339, 340, 341,
  342, 343, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355
);

DELETE FROM `wp_term_taxonomy` WHERE `term_taxonomy_id` IN (
  17, 15, 16, 18, 19, 21, 22, 23, 24, 25, 26, 28, 29, 30
);

DELETE FROM `wp_posts` WHERE `ID` = 356;

DELETE FROM `wp_postmeta` WHERE `meta_id` IN (
  556, 557, 558, 568
);

DELETE FROM `wp_options` WHERE `option_id` IN (
  851, 853, 855, 857, 859, 861, 864, 866, 868, 870, 872, 755, 754, 753, 752
);

update `wp_options` set `option_value` = 'http://www.enter.ru/content' where `option_name` in ('siteurl', 'home');

DELETE FROM `wp_posts` WHERE `ID` IN(
  338, 339, 340, 341, 342, 343, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355
);

UPDATE `wp_terms` SET `name` = 'Без рубрики', slug = '%d0%b1%d0%b5%d0%b7-%d1%80%d1%83%d0%b1%d1%80%d0%b8%d0%ba%d0%b8'
WHERE `term_id` = 1;

UPDATE `wp_term_taxonomy` SET `description` = '', `count` = 0
WHERE `term_taxonomy_id` = 1 AND `term_id` = 1;