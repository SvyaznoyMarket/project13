<?php

class myDoctrineTestDataLoadTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'test-data-load';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [myDoctrineTestDataLoad|INFO] task does things.
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
      'ProductCategory'    => 20,
      'ProductType'        => 50,
      'ProductProperty'    => 200,
      'News'               => 250,
      'Page'               => 20,
    );

    $this->logSection('doctrine', 'loading test Creators');
    $this->createRecordList('Creator', $count['Creator']);

    $this->logSection('doctrine', 'loading test ProductCategories');
    $this->createRecordList('ProductCategory', $count['ProductCategory']);
    $productCategoryTree = ProductCategoryTable::getInstance()->getTree();
    foreach (ProductCategoryTable::getInstance()->findAll() as $productCategory)
    {
      $productCategoryTree->createRoot($productCategory);

      $productCategoryCount = rand(2, 10);
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
    $productTypeList = $this->createRecordList('ProductType', $count['ProductType'], array('free' => false));

    $this->logSection('doctrine', 'loading test ProductProperties');
    $this->createRecordList('ProductProperty', $count['ProductProperty']);

    $this->logSection('doctrine', 'loading test ProductTypePropertyRelations, ProductFilterGroups and ProductFilters');
    $this->createRecordList('ProductFilterGroup', $count['ProductFilterGroup']);
    $list = ProductTypePropertyRelationTable::getInstance()->createList();
    for ($productType_id = 1; $productType_id < $count['ProductType']; $productType_id++)
    {
      $groupCount = rand(1, 3);

      $groupList = ProductPropertyGroupTable::getInstance()->createList();
      for ($i = 1; $i <= $groupCount; $i++)
      {
        $record = new ProductPropertyGroup();
        $record->fromArray(array(
          'name'            => $this->getRecordName('ProductPropertyGroup', $i),
          'product_type_id' => $productType_id,
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
          'product_type_id' => $productType_id,
          'property_id'     => $i + $propertyOffset,
          'group_id'        => $groupList[$group_index],
          'view_show'       => rand(0, 20) > 0 ? true : false,
          'view_list'       => rand(0, 8) > 0 ? true : false,
          'position'        => $i,
        ));
        $list[] = $record;
      }

      $productFilterList = ProductFilterTable::getInstance()->createList();
      $filterCount = rand(1, $propertyCount);
      for ($i = 1; $i <= $filterCount; $i++)
      {
        $record = new ProductFilter();
        $record->fromArray(array(
          'name'            => $this->getRecordName('ProductFilter', $i),
          'type'            => rand(0, 5) > 0 ? 'choice' : 'range',
          'group_id'        => $productType_id,
          'property_id'     => $i,
          'is_multiple'     => rand(0, 5) > 0 ? true : false,
          'position'        => $i,
        ));
        $productFilterList[] = $record;
      }
      $productFilterList->save();
      $productFilterList->free(true);
      unset($productFilterList);
    }
    $groupList->free(true);
    unset($groupList);

    $list->save();
    $list->free(true);
    unset($list);

    ProductCategoryTable::getInstance()->createQuery('productCategory')->query('UPDATE productCategory SET productCategory.filter_group_id = productCategory.id');

    $this->logSection('doctrine', 'loading test Products and ProductPropertyRelations');
    foreach ($productTypeList as $productType)
    {
      $list = ProductPropertyRelationTable::getInstance()->createList();
      $creatorCount = rand(4, 10);
      $creatorOffset = rand(1, $count['Creator'] - $creatorCount);

      $category_id = $productType->id; //rand(1, $count['ProductCategory']);

      $productCount = rand(4, 100);
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
        ));

        foreach ($productType->Property as $j => $property)
        {
          $relation = new ProductPropertyRelation();
          $relation->fromArray(array(
            'property_id'  => $property->id,
            'product_id'   => $record->id,
            'value'        => $this->getRecordName('ProductPropertyRelation', $productType->id.'-'.$i.'-'.($j + 1)),
            'unit'         => 'unit',
          ));

          $record->PropertyRelation[] = $relation;
        }

        $list[] = $record;
      }
      $list->save();

      $list->free(true);
      $productType->free(true);
      unset($list, $productType);
    }

/*---------- Creatung news --------------*/
    $this->logSection('doctrine', 'loading test News');
    for ($i = 1; $i <= $count['News']; $i++)
    {
      $record = new News();
      $record->fromArray(array(
        'name'          => $this->getRecordName('News', $i),
        'published_at'  => date('Y-m-d H:i:s', rand(strtotime('2009-01-01'), strtotime('now'))),
        'category_id'   => rand(1, 3),
      ));
      $record->save();
      $record->token = $record->id;
      $record->save();
    }
/*----------------------------------------*/


    $this->logSection('doctrine', 'loading test Pages');
    $this->createRecordList('Page', $count['Page']);
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
    );

    return $names[$model].'-'.$index;
  }
}
