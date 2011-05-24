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
      'Creator'         => 100,
      'ProductCategory' => 50,
      'ProductType'     => 50,
      'ProductProperty' => 200,
    );
    
    $this->logSection('doctrine', 'loading test Creators');
    $this->createRecordList('Creator', $count['Creator']);

    $this->logSection('doctrine', 'loading test ProductCategories');
    $this->createRecordList('ProductCategory', $count['ProductCategory']);

    $this->logSection('doctrine', 'loading test ProductTypes');
    $productTypeList = $this->createRecordList('ProductType', $count['ProductType'], array('free' => false));

    $this->logSection('doctrine', 'loading test ProductProperties');
    $this->createRecordList('ProductProperty', $count['ProductProperty']);

    $this->logSection('doctrine', 'loading test ProductTypePropertyRelations');
    $list = ProductTypePropertyRelationTable::getInstance()->createList();
    for ($productType_id = 1; $productType_id < $count['ProductType']; $productType_id++)
    {
      $propertyCount = rand(4, 12);
      $propertyOffset = rand(1, $count['ProductProperty'] - $propertyCount);
      
      for ($i = 1; $i <= $propertyCount; $i++)
      {
        $record = new ProductTypePropertyRelation();
        $record->fromArray(array(
          'product_type_id' => $productType_id,
          'property_id'     => $i + $propertyOffset,
          'position'        => $i,
        ));
        $list[] = $record;
      }
    }
    $list->save();
    $list->free(true);
    unset($list);
    
    $this->logSection('doctrine', 'loading test Products and ProductPropertyRelations');
    foreach ($productTypeList as $productType)
    {
      $list = ProductPropertyRelationTable::getInstance()->createList();
      $creatorCount = rand(4, 10);
      $creatorOffset = rand(1, $count['Creator'] - $creatorCount);

      $category_id = rand(1, count('ProductCategory'));
      
      $productCount = rand(4, 100);
      for ($i = 1; $i <= $productCount; $i++)
      {
        $record = new Product();
        $record->fromArray(array(
          'type_id'     => $productType->id,
          'creator_id'  => rand($creatorOffset, $creatorOffset + $creatorCount),
          'category_id' => $category_id,
          'name'        => 'product-'.$productType->id.'-'.$i,
        ));
        
        foreach ($productType->Property as $j => $property)
        {
          $relation = new ProductPropertyRelation();
          $relation->fromArray(array(
            'property_id'  => $property->id,
            'product_id'   => $record->id,
            'value_string' => 'property-'.$productType->id.'-'.$i.'-'.($j + 1),
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
  }
  
  protected function createRecordList($model, $count, array $options = array())
  {
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
          $options['nameField'] => lcfirst($model).'-'.$i,
        ));        
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
}
