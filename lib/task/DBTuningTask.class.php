<?php

class DBTuningTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'DBTuning';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [DBTuning|INFO] task does things.
Call it with:

  [php symfony DBTuning|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here

    $count = 0;
    //1) Установить price_list is_default
    $this->logSection('INFO', 'Установливаю price_list is_default');
    $count = $connection->exec("UPDATE `product_price_list` SET `is_default` = 1 WHERE `id` = 1");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //2) Установить region is_default
    $this->logSection('INFO', 'Устанавливаю region is_default');
    $count = $connection->exec("UPDATE `region` SET `is_default` = 1 WHERE `token` = 'moskva'");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //3) В region установить для всех type=area, кроме Россия и Москва
    $this->logSection('INFO', 'Устанавливаю в region для всех type=area, кроме Россия и Москва');
    $count = $connection->exec("UPDATE `region` SET `type` = 'area' WHERE `token` NOT IN ('russia', 'moskva')");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //4) Для рут категорий в product_category установить position как для меню
    $this->logSection('INFO', 'Устанавливаю position как в меню для рут категорий в product_category');
    $count = $connection->exec("UPDATE `product_category` SET `position` = FIELD(`name`, 'Мебель', 'Бытовая техника', 'Товары для дома', 'Товары для детей', 'Сделай сам (Инструменты)', 'Электроника', 'Ювелирные украшения и часы', 'Товары для спорта и отдыха', 'Подарки и хобби') WHERE `level` = 0");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //5) Обновить у product поля is_instock, view_list, view_show
    $this->logSection('INFO', 'Обновляю у product поля is_instock, view_list, view_show');
    $count = $connection->exec("UPDATE `product` `p` INNER JOIN `stock_product_relation` `sp` ON `sp`.`product_id` = `p`.`id` INNER JOIN `product_price` `pp` ON `pp`.`product_id` = `p`.`id` INNER JOIN `product_photo` `ph` ON `ph`.`product_id` = `p`.`id` SET `p`.`is_instock` = 1, `p`.`view_list` = 1, `p`.`view_show` = 1 WHERE `p`.`name` <> ''");
    $count += $connection->exec("UPDATE `product` `p` INNER JOIN `product_category_product_relation` `pcp` ON `pcp`.`product_id` = `p`.`id` INNER JOIN `product_category` `pc` ON `pc`.`id` = `pcp`.`product_category_id` AND `pc`.`name` = 'Мебель' INNER JOIN `product_price` `pp` ON `pp`.`product_id` = `p`.`id` INNER JOIN `product_photo` `ph` ON `ph`.`product_id` = `p`.`id` SET `p`.`is_instock` = 1, `p`.`view_list` = 1, `p`.`view_show` = 1 WHERE `p`.`name` <> ''");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //6) Сделать update значений характеристик: поменять true и false на "да" и "нет"
    $this->logSection('INFO', 'Делаю update значений характеристик: поменять true и false на "да" и "нет"');
    $count = $connection->exec("UPDATE `product_property_relation` SET `value` = ELT(FIELD(`value`, 'true', 'false'), 'есть', 'нет') WHERE `value` IN ('true', 'false')");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    $this->logSection('INFO', 'Добавляем во все категории группы тэгов');

    $categories = ProductCategoryTable::getInstance()->findAll();
    $connection->exec("TRUNCATE `tag_group_product_category_relation`");

    $count = 0;
    foreach ($categories as $category)
    {
      $descendantIds = $category->getDescendantIds();
      if (!count($descendantIds)) continue;

      $q = ProductTypeTable::getInstance()->createBaseQuery();
      $q->select('productType.*')
        ->innerJoin('productType.Product product WITH product.is_instock = ?', 1)
        ->innerJoin('product.CategoryRelation categoryRelation')
        ->andWhereIn('categoryRelation.product_category_id', $descendantIds)
     ;
      $productTypeIds = ProductTypeTable::getInstance()->getIdsByQuery($q);
      if (!count($productTypeIds))
      {
        $this->logSection('INFO', 'Категоря '.$category->name.' не содержит товаров');
        continue;
      }

      $sql = "INSERT IGNORE INTO `tag_group_product_category_relation` (`product_category_id`, `tag_group_id`) SELECT DISTINCT ".$category->id.", `tgpt`.`tag_group_id` FROM `tag_group_product_type_relation` `tgpt` WHERE `tgpt`.`product_type_id` IN (".implode(", ", $productTypeIds).")";
      try
      {
       $count += $connection->exec($sql);
      }
      catch (Exception $e)
      {
        $this->logSection('ERR', $e->getMessage());
      }
    }
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //7) Установить для самовывоза token=self
    $this->logSection('INFO', 'Установить для самовывоза token=self');
    $count = $connection->exec("UPDATE `delivery_type` SET `token` = 'self' WHERE `name` = 'Заберу на месте'");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');

    //8) устанавливаю position для группы тэгов
    $this->logSection('INFO', 'Устанавливаю position для группы тэгов');
    $count = $connection->exec("UPDATE `tag_group` `tg` SET `tg`.`position` = (SELECT sum(`tgpt`.`position`) FROM `tag_group_product_type_relation` `tgpt` WHERE `tag_group_id` = `tg`.`id` GROUP BY `tgpt`.`tag_group_id`)");
    $this->logSection('INFO', 'Обновлено: '.$count.' записей');
  }
}
