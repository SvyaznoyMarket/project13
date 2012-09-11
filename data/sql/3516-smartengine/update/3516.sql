CREATE TABLE IF NOT EXISTS `queue` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `locked_at` timestamp NULL DEFAULT NULL COMMENT 'время блокировки',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'название обработчика',
  `body` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'json-данные для обработчика',
  PRIMARY KEY (`id`),
  KEY `locked_at-name` (`locked_at`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;