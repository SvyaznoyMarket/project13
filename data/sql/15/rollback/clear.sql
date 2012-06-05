CREATE TABLE `product_helper` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` bigint(20) unsigned NOT NULL COMMENT 'тип продукта',
  `is_active` tinyint(1) NOT NULL,
  `token` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL COMMENT 'Порядок сортировки',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `product_helper` (`type_id`),
  CONSTRAINT `product_helper` FOREIGN KEY (`type_id`) REFERENCES `product_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Помошник выбора товара';
CREATE TABLE `product_helper_answer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `condition_id` tinyint(3) unsigned NOT NULL COMMENT 'условие набора: 1-and, 2-or',
  `position` int(11) NOT NULL COMMENT 'Порядок сортировки',
  PRIMARY KEY (`id`),
  KEY `question_id_idx` (`question_id`),
  CONSTRAINT `product_helper_answer-product_helper_question` FOREIGN KEY (`question_id`) REFERENCES `product_helper_question` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ответ для помошника выбора товара';
CREATE TABLE `product_helper_filter` (
  `answer_id` bigint(20) unsigned NOT NULL,
  `filter_id` bigint(20) unsigned NOT NULL,
  `value` text COMMENT 'значение фильтра товара в формате yaml',
  PRIMARY KEY (`answer_id`,`filter_id`),
  CONSTRAINT `product_helper_filter-product_helper_answer` FOREIGN KEY (`answer_id`) REFERENCES `product_helper_answer` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Связь ответа для помошника выбора товара и свойства товара';
CREATE TABLE `product_helper_question` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `helper_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` int(11) NOT NULL COMMENT 'Порядок сортировки',
  PRIMARY KEY (`id`),
  KEY `helper_id_idx` (`helper_id`),
  CONSTRAINT `product_helper_question-product_helper` FOREIGN KEY (`helper_id`) REFERENCES `product_helper` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Вопрос для помошника выбора товара';
