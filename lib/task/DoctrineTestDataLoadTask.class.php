<?php

class DoctrineTestDataLoadTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'test-data-load';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [DoctrineTestDataLoad|INFO] task does things.
Call it with:

  [php symfony myDoctrineTestDataLoad|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $this->logSection('doctrine', 'loading test data');
    $count = array(
      'Creator'            => 100,
      'ProductCategory'    => 30,
      'ProductType'        => 30,
      'ProductProperty'    => 200,
      'News'               => 250,
      'Page'               => 10,
      'ProductHelper'      => 4,
      'Stock'              => 30,
      'Shop'               => 20,
    );

    $this->logSection('doctrine', 'loading test Creators');
    $this->createRecordList('Creator', $count['Creator']);

    $this->logSection('doctrine', 'loading test Shops');
    for ($i = 1; $i <= $count['Shop']; $i++)
    {
      $record = new Shop();
      $record->fromArray(array(
        'token'     => 'shop-'.$i,
        'name'      => $this->getRecordName('Shop', $i),
        'region_id' => 1, //(rand(1, 5) > 1 ? 6 : 5),
        'address'   => 'Адресс '.$i,
      ));
      $record->save();
    }

    $this->logSection('doctrine', 'loading test Stocks');
    for ($i = 1; $i <= $count['Stock']; $i++)
    {
      $shop = rand(1, 5) > 1 ? null : ShopTable::getInstance()->find(rand(1, $count['Shop']));
      $region_id =
        $shop
        ? $shop->region_id
        : 1 //(rand(1, 6) > 1 ? 6 : 5)
      ;

      $record = new Stock();
      $record->fromArray(array(
        'token'     => 'stock-'.$i,
        'name'      => $this->getRecordName('Stock', $i),
        'region_id' => $region_id,
        'shop_id'   => $shop ? $shop->id : null,
      ));
      $record->save();
    }

    $this->logSection('doctrine', 'loading test ProductCategories');
    $productCategoryTree = ProductCategoryTable::getInstance()->getTree();
    foreach ($productCategoryTree->fetchRoots() as $productCategory)
    {
      //$productCategoryTree->createRoot($productCategory);

      $productCategoryCount = rand(5, 6);
      for ($i = 1; $i <= $productCategoryCount; $i++)
      {
        $child = new ProductCategory();
        $child->fromArray(array(
          'name'  => $this->getRecordName('ProductCategory', $i),
          'token' => 'category-'.$productCategory->id.'-'.$i,
        ));
        $child->getNode()->insertAsLastChildOf($productCategory);
      }
    }
    $count['ProductFilterGroup'] = ProductCategoryTable::getInstance()->createQuery()->count();

    $this->logSection('doctrine', 'loading test ProductTypes');
    $productTypeList = ProductTypeTable::getInstance()->createList();
    for ($i = 1; $i <= $count['ProductType']; $i++)
    {
      $record = new ProductType();
      $record->fromArray(array(
        'token'          => 'productType-'.$i,
        'name'           => $this->getRecordName('ProductType', $i),
        'rating_type_id' => 1,
      ));
      $productTypeList[] = $record;
    }
    $productTypeList->save();

    $this->logSection('doctrine', 'loading test ProductProperties');
    $productPropertyList = ProductPropertyTable::getInstance()->createList();
    for ($i = 1; $i <= $count['ProductProperty']; $i++)
    {
      $record = new ProductProperty();
      $record->fromArray(array(
        'name'        => $this->getRecordName('ProductProperty', $i),
        'type'        => rand(0, 6) > 0 ? 'select' : 'string',
        'is_multiple' => rand(0, 3) > 0 ? false : true,
        'unit'        => null,
        'pattern'     => null,
      ));
      $productPropertyList[] = $record;
    }
    $productPropertyList->save();

    foreach ($productPropertyList as $productProperty)
    {
      if ('select' == $productProperty->type)
      {
        $optionCount = rand(4, 8);

        $list = ProductPropertyOptionTable::getInstance()->createList();
        for ($i = 1; $i < $optionCount; $i++)
        {
          $record = new ProductPropertyOption();
          $record->fromArray(array(
            'property_id' => $productProperty->id,
            'value'       => 'значение-'.$i,
            'position'    => $i,
          ));

          $list[] = $record;
        }

        $list->save();
        $list->free(true);
        unset($list);
      }
    }

    $this->logSection('doctrine', 'loading test ProductTypePropertyRelations');
    $list = ProductTypePropertyRelationTable::getInstance()->createList();
    foreach ($productTypeList as $productType)
    {
      $groupCount = rand(1, 3);

      $groupList = ProductPropertyGroupTable::getInstance()->createList();
      for ($i = 1; $i <= $groupCount; $i++)
      {
        $record = new ProductPropertyGroup();
        $record->fromArray(array(
          'name'            => $this->getRecordName('ProductPropertyGroup', $i),
          'product_type_id' => $productType->id,
          'position'        => $i,
        ));
        $groupList[] = $record;
      }
      $groupList->save();

      $propertyCount = rand(2, 6) * $groupCount;
      $propertyOffset = rand(1, $count['ProductProperty'] - $propertyCount);

      for ($i = 1; $i <= $propertyCount; $i++)
      {
        $group_index = rand(0, $groupCount - 1);

        $record = new ProductTypePropertyRelation();
        $record->fromArray(array(
          'product_type_id' => $productType->id,
          'property_id'     => $i + $propertyOffset,
          'group_id'        => $groupList[$group_index],
          'view_show'       => rand(0, 20) > 0 ? true : false,
          'view_list'       => rand(0, 8) > 0 ? true : false,
          'position'        => $i,
        ));
        $list[] = $record;
      }
    }
    $groupList->free(true);
    unset($groupList);

    $list->save();
    $list->free(true);
    unset($list);


    $this->logSection('doctrine', 'loading test ProductFilterGroups and ProductFilters');
    $this->createRecordList('ProductFilterGroup', $count['ProductFilterGroup']);
    foreach ($productTypeList as $productType)
    {
      $list = ProductFilterTable::getInstance()->createList();
      foreach ($productType->Property as $productProperty)
      {
        if (0 == rand(0, 1)) continue;

        $record = new ProductFilter();
        $record->fromArray(array(
          'name'            => $this->getRecordName('ProductFilter', $i),
          'type'            => 'select' == $productProperty->type ? 'choice' : 'range',
          'group_id'        => $productType->id,
          'property_id'     => $productProperty->id,
          'is_multiple'     => rand(0, 5) > 0 ? true : false,
          'position'        => $i,
        ));
        $list[] = $record;
      }
      $list->save();
      $list->free(true);
      unset($list);
    }


    ProductCategoryTable::getInstance()->createQuery('productCategory')->query('UPDATE productCategory SET productCategory.filter_group_id = productCategory.id');

    $this->logSection('doctrine', 'loading test Products, ProductPropertyRelations and StockProductRelations');
    foreach ($productTypeList as $productType)
    {
      $list = ProductPropertyRelationTable::getInstance()->createList();
      $creatorCount = rand(4, 10);
      $creatorOffset = rand(1, $count['Creator'] - $creatorCount);

      $category_id = $productType->id; //rand(1, $count['ProductCategory']);

      $productCount = rand(0, 20) > 0 ? rand(15, 25) : rand(25, 55);
      for ($i = 1; $i <= $productCount; $i++)
      {
        $record = new Product();
        $record->fromArray(array(
          'type_id'     => $productType->id,
          'creator_id'  => rand($creatorOffset, $creatorOffset + $creatorCount),
          'category_id' => $category_id,
          'token'       => 'product-'.$productType->id.'-'.$i,
          'name'        => $this->getRecordName('Product', $productType->id.'-'.$i),
          'view_show'   => rand(0, 10) > 0 ? true : false,
          'view_list'   => rand(0, 50) > 0 ? true : false,
          'is_instock'  => rand(0, 30) > 0 ? true : false,
          'price'       => round(rand(500, 80000), -2),
          'rating'      => rand(0, 50) / 10,
        ));

        foreach ($productType->Property as $j => $productProperty)
        {
          $relation = new ProductPropertyRelation();

          $offset = rand(0, $productProperty->Option->count() - 1);
          $option_id = null;
          if ('select' == $productProperty->type)
          {
            $offset = rand(0, $productProperty->Option->count() - 1);
            $option_id = $productProperty->Option[$offset]->id;
          }

          $relation->fromArray(array(
            'property_id'  => $productProperty->id,
            'option_id'    => $option_id,
            'value'        => $option_id ? null : $this->getRecordName('ProductPropertyRelation', $productType->id.'-'.$i.'-'.($j + 1)),
            'unit'         => 'unit',
          ));

          $record->PropertyRelation[] = $relation;
        }

        $stockRelationCount = rand(0, $count['Stock']);
        for ($k = 1; $k < $stockRelationCount; $k++)
        {
          $relation = new StockProductRelation();
          $relation->fromArray(array(
            'stock_id' => $k,
            'quantity' => rand(1, 25),
          ));

          $record->StockRelation[] = $relation;
        }

        $list[] = $record;
      }
      $list->save();

      $list->free(true);
      unset($list);
    }

    $this->logSection('doctrine', 'loading test News');
    for ($i = 1; $i <= $count['News']; $i++)
    {
      $record = new News();
      $record->fromArray(array(
        'token'         => 'news-'.$i,
        'name'          => $this->getRecordName('News', $i),
        'published_at'  => date('Y-m-d H:i:s', rand(strtotime('2009-01-01'), strtotime('now'))),
        'category_id'   => rand(1, 3),
        'is_active'     => rand(0, 3) > 0 ? true : false,
      ));
      $record->save();
    }

    $this->logSection('doctrine', 'loading test Pages');
    $this->createRecordList('Page', $count['Page']);

    $this->logSection('doctrine', 'loading test ProductHelpers');
    $list = ProductHelperTable::getInstance()->createList();
    for ($productHelper_id = 1; $productHelper_id <= $count['ProductHelper']; $productHelper_id++)
    {
      $record = new ProductHelper();
      $record->fromArray(array(
        'product_type_id' => rand(1, $count['ProductType']),
        'token'           => 'product-helper-'.$productHelper_id,
        'name'            => $this->getRecordName('ProductHelper', $productHelper_id),
        'is_active'       => true,
      ));

      $productHelperQuestion_count = rand(3, 5);
      for ($i = 1; $i <= $productHelperQuestion_count; $i ++)
      {
        $productHelperQuestion = new ProductHelperQuestion();
        $productHelperQuestion->fromArray(array(
          'name'      => 'вопрос '.$i,
          'position'  => $i,
          'is_active' => true,
        ));

        $productHelperAnswer_count = rand(2, 4);
        for ($j = 1; $j <= $productHelperAnswer_count; $j++)
        {
          $productHelperAnswer = new ProductHelperAnswer();
          $productHelperAnswer->fromArray(array(
            'name'      => 'ответ '.$j,
            'position'  => $j,
            'is_active' => true,
          ));
          $productHelperQuestion->Answer[] = $productHelperAnswer;
        }

        $record->Question[] = $productHelperQuestion;
      }

      $list[] = $record;
    }
    $list->save();

    $this->logSection('doctrine', 'loading test SimilarProduct');
    $product_type = rand(1, $count['ProductType']);
    $connection->query("INSERT INTO `similar_product_group` (`id`, `product_type_id`, `name`, `products`, `match`, `price`) VALUES (1, NULL, 'Группа-1', '432,4,765,43, 756, 81, 47', NULL, NULL), (2, ".$product_type.", 'Группа-2', NULL, 3, 0.1)");
    $properties = $connection->query("SELECT `property_id` FROM `product_type_property_relation` WHERE `product_type_id` = ".$product_type." LIMIT 3")->fetchAll();
    foreach ($properties as &$property)
    {
      $property = "(2, ".$property['property_id'].")";
    }
    $connection->query("INSERT INTO `similar_product_property` (`group_id`, `property_id`) VALUES ".implode(", ", $properties));
  }

  protected function createRecordList($model, $count, array $options = array())
  {
    $hasToken = Doctrine_Core::getTable($model)->hasColumn('token');

    $options = myToolkit::arrayDeepMerge(array(
      'nameField' => 'name',
      'free'      => true,
    ), $options);

    $list = Doctrine_Core::getTable($model)->createList();
    for ($i = 1; $i <= $count; $i++)
    {
      $record = new $model();
      if ($options['nameField'])
      {
        $record->fromArray(array(
          $options['nameField'] => $this->getRecordName($model, $i),
        ));
        if ($hasToken)
        {
          $record->token = lcfirst($model).'-'.$i;
        }
      }
      $list[] = $record;
    }
    $list->save();

    if ($options['free'])
    {
      $list->free(true);
      unset($list);

      return false;
    }

    return $list;
  }

  protected function getRecordName($model, $index)
  {
    $names = array(
      'Creator'                 => 'производитель',
      'ProductType'             => 'схема',
      'Product'                 => 'товар',
      'ProductCategory'         => 'категория',
      'ProductPropertyGroup'    => 'группа',
      'ProductProperty'         => 'свойство',
      'ProductPropertyRelation' => 'значение',
      'ProductFilterGroup'      => 'группа',
      'ProductFilter'           => 'фильтр',
      'News'                    => 'новость',
      'Page'                    => 'страница',
      'ProductHelper'           => 'помошник',
      'Stock'                   => 'склад',
      'Shop'                    => 'магазин',
    );

    return $names[$model].'-'.$index;
  }
}
