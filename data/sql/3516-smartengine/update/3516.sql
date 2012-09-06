CREATE TABLE IF NOT EXISTS `queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'флаг блокировки',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'название обработчика',
  `body` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'json-данные для обработчика',
  PRIMARY KEY (`id`),
  KEY `is_locked-name` (`is_locked`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;