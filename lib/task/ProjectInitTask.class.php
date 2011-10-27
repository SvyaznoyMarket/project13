<?php

class ProjectInitTask extends sfBaseTask
{
  protected
    $connection = null,
    $collections = array(),
    $logger = null,
    $task = null
  ;

  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('task_id', sfCommandArgument::REQUIRED, 'Task id'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('dump', null, sfCommandOption::PARAMETER_NONE, 'Only dump response'),
      new sfCommandOption('freeze', null, sfCommandOption::PARAMETER_NONE, 'Freeze executing next packet'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'init';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectInit|INFO] task does things.
Call it with:

  [php symfony ProjectInit|INFO]
EOF;
    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/init.log'));
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfConfig::set('sf_logging_enabled', $options['log']);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $this->task = TaskTable::getInstance()->find($arguments['task_id']);
    if ('success' == $this->task->status)
    {
      return true;
    }

    $params = $this->task->getContentData();
    if (!isset($params['packet_id']))
    {
      return false;
    }

    $this->connection->exec('SET foreign_key_checks = 0');

    // add your code here
    $core = Core::getInstance();

    $this->logSection('core', 'loading packet #'.$params['packet_id']);
    $response = $core->query('load.get', array(
      'id' => $params['packet_id'],
    ));
    if ($options['dump'])
    {
      myDebug::dump($response, true, 'yaml');

      return;
    }

    if (!is_array($response))
    {
      $this->logSection('core', "error\n".sfYaml::dump($response));

      return false;
    }

    if (!empty($response['result']) && ('empty' == $response['result']))
    {
      $this->logSection('core', 'empty result', null, 'ERROR');

      return;
    }

    foreach ($response as $item)
    {
      foreach ($item['data'] as $entity)
      {
        if ($table = $core->getTable($entity['type']))
        {
          $this->log($table->getComponentName().' <- '.$entity['type'].' #'.$entity['id']);

          try {
            $method = 'create'.$table->getComponentName().'Record';
            $record =
              method_exists($this, $method)
              ? call_user_func_array(array($this, $method), array($entity['data']))
              : $table->createRecordFromCore($entity['data'])
            ;

            if ($record)
            {
              $this->pushRecord($record);
            }
          }
          catch (Exception $e) {
            $this->logSection('Import entity', $entity['type'].' #'.$entity['id'].' error: '.$e->getMessage(), null, 'ERROR');
          }
        }
        elseif (method_exists($this, 'process'.ucfirst($entity['type'])))
        {
          try {
            if ($record = call_user_func_array(array($this, 'process'.ucfirst($entity['type'])), array($entity['data'], )))
            {
              $this->pushRecord($record);
            }
          }
          catch (Exception $e) {
            $this->logSection('Import entity', $entity['type'].' #'.$entity['id'].' error: '.$e->getMessage(), null, 'ERROR');
          }
        }
        else {
          $this->logSection('Unknown entity', $entity['type'].' #'.$entity['id'], null, 'ERROR');
          $this->logger->log('Unknown entity: '.$entity['type'].' #'.$entity['id']);
        }
      }
    }
    $nextPacketId = $item['next_id'];

    $this->flushCollections();

    $this->connection->exec('SET foreign_key_checks = 1');

    if (!$options['freeze'])
    {
      $this->task->setContentData(array(
        'packet_id' => $nextPacketId, //4508
      ));
      $this->task->save();
    }

    if (null === $nextPacketId)
    {
      $this->task->status = 'success';
      $this->task->save();
    }
  }

  protected function getRecordByCoreId($model, $coreId, $returnId = false)
  {
    return myDoctrineTable::getRecordByCoreId($model, $coreId, $returnId);
  }

  /**
   * Заносит запись в соответствующую коллекцию
   *
   * @param myDoctrineRecord $record
   */
  protected function pushRecord(myDoctrineRecord $record)
  {
    $name = $record->getTable()->getComponentName();

    if (!isset($this->collections[$name]))
    {
      $this->collections[$name] = $record->getTable()->createList();
    }

    $this->collections[$name][] = $record;
  }

  /**
   * Сохраняет коллекции в бд
   */
  protected function flushCollections()
  {
    //$this->logSection('collection', 'flush...');
    foreach ($this->collections as $name => $collection)
    {
      $prepared = $this->task->getContentData('prepared');

      // если таблица для модели не подготовлена
      if (!in_array($name, $prepared))
      {
        $this->logSection($name, 'prepare...');
        $method = 'prepare'.$name.'Table';
        if (method_exists($this, $method))
        {
          call_user_func_array(array($this, $method), array());
        }
        else {
          $this->connection->exec('TRUNCATE TABLE `'.Doctrine_Core::getTable($name)->getTableName().'`');
        }

        $prepared[] = $name;
        $this->task->setContentData('prepared', $prepared);
        $this->task->save();
      }

      $this->logSection($name, 'flush...');
      $method = 'flush'.$name.'Collection';

      // disable core pushing
      foreach ($collection as $record)
      {
        $record->setCorePush(false);
      }

      if (method_exists($this, $method))
      {
        //if ('ProductCategory' == $name)          myDebug::dump($collection, 1);
        call_user_func_array(array($this, $method), array($collection));
      }
      else
      {
        //myDebug::dump($name);
        $collection->save();
      }

      $this->log('...'.Doctrine_Core::getTable($name)->createQuery()->count());

      $collection->free();
      $collection = null;
      unset($this->collections[$name]);
    }
  }

  // FilterGroup
  protected function flushProductFilterCollection(myDoctrineCollection $collection)
  {
    foreach ($collection as $record)
    {
      $q = Doctrine_Query::create()
        ->from('ProductFilter productFilter')
        ->where('productFilter.group_id = ? AND productFilter.property_id = ?', array($record->group_id, $record->property_id,));
      if (!$q->fetchOne())
      {
        $record->save();
      }
    }
  }

  // Region
  protected function prepareRegionTable()
  {
    $this->logSection('Region', 'prepare table...');

    $table = RegionTable::getInstance();

    $root = $table->getTree()->fetchRoot()->toArray();
    $root = myToolkit::arrayDeepMerge($root, array(
      'root_id' => 1,
      'lft'     => 1,
      'rgt'     => 2,
      'level'   => 0
    ));
    $this->connection->exec('TRUNCATE TABLE '.$table->getTableName());

    $record = new Region();
    $record->fromArray($root);
    $record->save();
    $table->getTree()->createRoot($record);
  }
  // Region
  protected function createRegionRecord(array $data)
  {
    $record = RegionTable::getInstance()->createRecordFromCore($data);
    $record->token = myToolkit::urlize($record->name);

    return $record;
  }
  // Region
  protected function flushRegionCollection(myDoctrineCollection $collection)
  {
    $table = RegionTable::getInstance();

    $collection->save();

    // создает двухуровневое дерево
    $root = $table->getTree()->fetchRoot();
    foreach ($collection as $record)
    {
      $record->getNode()->insertAsLastChildOf($root);
    }

    // формирует уровни дерева
    for ($level = 1; $level <= 6; $level++)
    {
      foreach ($collection as $record)
      {
        $record->refresh();
        $node = $record->getNode();
        if ($node->isRoot()) continue;

        if ($level == $record->level)
        {
          $parent = $record->core_parent_id ? $table->findOneByCoreId($record->core_parent_id) : false;
          if ($parent && $node->getParent())
          {
            if ($parent->id != $node->getParent()->id)
            {
              //myDebug::dump($parent->id.' -> '.$record->id);
              $node->moveAsLastChildOf($parent);
            }
          }
        }
      }
    }

    // угадывает тип региона
    foreach ($table->getTree()->fetchTree() as $record)
    {
      if ($record->getNode()->isRoot()) continue;

      $record->type =
        $record->getNode()->hasChildren()
        ? 'area'
        : 'city'
      ;
      $record->save();
    }
  }

  //TagGroup
  protected function prepareTagGroupTable()
  {
    $this->logSection('ProductCategory', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `tag_group`');
    $this->connection->exec('TRUNCATE TABLE `tag_group_product_type_relation`');
    $this->connection->exec('TRUNCATE TABLE `tag_group_relation`');
  }

  // TagGroup
  protected function createTagGroupRecord(array $data)
  {
    $record = TagGroupTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }

  // Tag
  protected function createTagRecord(array $data)
  {
    $record = TagTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }

  // ProductCategory
  protected function prepareProductCategoryTable()
  {
    $this->logSection('ProductCategory', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `product_category`');
    $this->connection->exec('TRUNCATE TABLE `product_filter_group`');
  }
  // ProductCategory
  protected function createProductCategoryRecord(array $data)
  {
    $record = ProductCategoryTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    $filter = new ProductFilterGroup();
    $filter->fromArray(array(
      'name' => 'Фильтр для '.$record->name,
    ));

    $record->FilterGroup = $filter;

    return $record;
  }
  // ProductCategory
  protected function flushProductCategoryCollection(myDoctrineCollection $collection)
  {
    $made = $this->task->getContentData('made');
    $name = 'ProductCategory';

    if (!in_array($name, $made))
    {

      $table = ProductCategoryTable::getInstance();

      $tree = $table->getTree();

      // создает двухуровневое дерево
      foreach ($collection as $record)
      {
        if (empty($record->core_parent_id))
        {
          $record->save();
          $tree->createRoot($record);
        }
      }

      // формирует уровни дерева
      for ($level = 0; $level <= 6; $level++)
      {
        foreach ($table->findByLevel($level) as $parent)
        {
          foreach ($collection as $i => $record)
          {
            if ($record->core_parent_id != $parent->core_id) continue;

            $record->getNode()->insertAsLastChildOf($parent);

            // free memory
            $collection[$i]->free(true);
            $collection[$i] = null;
            unset($collection[$i]);
          }
        }
      }

      $made[] = $name;
      $this->task->setContentData('made', $made);
      $this->task->save();
    }
    else
    {
      $collection->save();
    }
  }

  // Creator
  protected function createCreatorRecord(array $data)
  {
    $record = CreatorTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }

  // Shop
  protected function createShopRecord(array $data)
  {
    $record = ShopTable::getInstance()->createRecordFromCore($data);
    $record->token = myToolkit::urlize($record->name);

    return $record;
  }

  // ProductPropertyOption
  protected function createProductPropertyOptionRecord(array $data)
  {
    $record = ProductPropertyOptionTable::getInstance()->createRecordFromCore($data);

    return $record;
  }

  // ProductType
  protected function prepareProductTypeTable()
  {
    $this->logSection('ProductType', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `product_type`');
    $this->connection->exec('TRUNCATE TABLE `product_type_property_relation`');
    $this->connection->exec('TRUNCATE TABLE `product_type_property_group_relation`');
    $this->connection->exec('TRUNCATE TABLE `product_category_type_relation`');
    $this->connection->exec('TRUNCATE TABLE `tag_group_product_type_relation`');
  }
  // ProductType
  protected function createProductTypeRecord(array $data)
  {
    $record = ProductTypeTable::getInstance()->createRecordFromCore($data);
    $record->token = myToolkit::urlize($record->name);

    if (!empty($data['property'])) foreach ($data['property'] as $relationData)
    {
      if ($relationData['is_filter'] && !empty($data['category']))
      {
        foreach ($data['category'] as $category)
        {
          $filter = new ProductFilter();
          $filter->fromArray(array(
            'name'        => $relationData['name'],
            'type'        =>  (6 == $relationData['filter_type_id']) ? 'range' : 'choice',
            'property_id' => $this->getRecordByCoreId('ProductProperty', $relationData['id'], true),
            'group_id'    => $this->getRecordByCoreId('ProductCategory', $category['id'])->FilterGroup->id,
            'position'    => $relationData['filter_position'],
            'is_multiple' => $relationData['is_multiple'],
          ));

          $this->pushRecord($filter);
        }
      }
    }

    return $record;
  }

  // Product
  protected function prepareProductTable()
  {
    $this->logSection('Product', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `product`');
    $this->connection->exec('TRUNCATE TABLE `product_property_relation`');
    $this->connection->exec('TRUNCATE TABLE `product_category_product_relation`');
    $this->connection->exec('TRUNCATE TABLE `tag_product_relation`');
  }

  // Product
  protected function createProductRecord(array $data)
  {
    $record = ProductTable::getInstance()->createRecordFromCore($data);
    $record->token = !empty($data['bar_code']) ? $data['bar_code'] : uniqid();

    return $record;
  }

  // ProductComment
  protected function createProductCommentRecord(array $data)
  {
    $record = ProductCommentTable::getInstance()->createRecordFromCore($data);

    return $record;
  }
  // ProductComment
  protected function flushProductCommentCollection(myDoctrineCollection $collection)
  {
    $table = ProductCommentTable::getInstance();

    $tree = $table->getTree();

    // создает двухуровневое дерево
    foreach ($collection as $record)
    {
      if (empty($record->core_parent_id))
      {
        $record->save();
        $tree->createRoot($record);
      }
    }

    // формирует уровни дерева
    for ($level = 0; $level <= 10; $level++)
    {
      foreach ($table->findByLevel($level) as $parent)
      {
        foreach ($collection as $i => $record)
        {
          if ($record->core_parent_id != $parent->core_id) continue;

          $record->getNode()->insertAsLastChildOf($parent);

          // free memory
          $collection[$i]->free(true);
          $collection[$i] = null;
          unset($collection[$i]);
        }
      }
    }
  }

  // Product
  protected function prepareServiceCategoryTable()
  {
    $this->logSection('Service category', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `service_category`');
  }

  // ServiceCategory
  protected function createServiceCategoryRecord(array $data)
  {
    $record = ServiceCategoryTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);
    $record->is_active = 1;

    return $record;
  }

  // ServiceCategory
  protected function flushServiceCategoryCollection(myDoctrineCollection $collection)
  {
    $table = ServiceCategoryTable::getInstance();

    $tree = $table->getTree();

    // создает двухуровневое дерево
    foreach ($collection as $record)
    {
      if (empty($record->core_parent_id))
      {
        $record->save();
        $tree->createRoot($record);
      }
    }

    // формирует уровни дерева
    for ($level = 0; $level <= 6; $level++)
    {
      foreach ($table->findByLevel($level) as $parent)
      {
        foreach ($collection as $i => $record)
        {
          if ($record->core_parent_id != $parent->core_id) continue;

          $record->getNode()->insertAsLastChildOf($parent);

          // free memory
          $collection[$i]->free(true);
          $collection[$i] = null;
          unset($collection[$i]);
        }
      }
    }
  }

  // ProductPrice
  protected function createProductPriceRecord(array $data)
  {
    $record = ProductPriceTable::getInstance()->createRecordFromCore($data);

    return $record;
  }

  // Region
  protected function prepareServiceTable()
  {
    $this->logSection('Service', 'prepare table...');

    $this->connection->exec('TRUNCATE TABLE `service`');
    $this->connection->exec('TRUNCATE TABLE `service_category_relation`');
  }

  //Service
  protected function createServiceRecord(array $data)
  {
    $record = ServiceTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }

  //PaymentMethod
  protected function createPaymentMethodRecord(array $data)
  {
    $record = PaymentMethodTable::getInstance()->createRecordFromCore($data);
    $record->token = myToolkit::urlize($record->name);

    return $record;
  }

  //DeliveryType
  protected function createDeliveryTypeRecord(array $data)
  {
    $record = DeliveryTypeTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }

  //StockProductRelation
  protected function createStockProductRelationRecord(array $data)
  {
    $record = StockProductRelationTable::getInstance()->createRecordFromCore($data);

    if (false
      || (empty($data['store_id']) && empty($data['shop_id']))
      || (!$record->stock_id && !$record->shop_id)
      || !$record->product_id
    ) {
      return false;
    }

    return $record;
  }

  //StockRelation
  protected function createStockRecord(array $data)
  {
    $record = StockTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid();

    return $record;
  }

  //DeliveryPeriod
  protected function createDeliveryPeriodRecord(array $data)
  {
    $record = DeliveryPeriodTable::getInstance()->createRecordFromCore($data);
    //$record->delivery_type_id = $this->getRecordByCoreId('', $data['delivery_type_id'], true);

    return $record;
  }

  // Order
  protected function createOrderRecord(array $data)
  {
    $record = OrderTable::getInstance()->createRecordFromCore($data);
    $record->token = uniqid().'-'.$record->token;

    return $record;
  }

  //Photo for everything
  protected function processUpload(array $data)
  {
    $record = null;
    switch ($data['item_type_id'])
    {
      case 1:
        switch ($data['type_id'])
        {
          case 1:
            $record = ProductPhotoTable::getInstance()->createRecordFromCore($data);
            $record->product_id = $this->getRecordByCoreId('Product', $data['item_id'], true);
            $record->view_show = 1;
            break;
          case 2:
            $record = ProductPhoto3DTable::getInstance()->createRecordFromCore($data);
            $record->product_id = $this->getRecordByCoreId('Product', $data['item_id'], true);
            break;
        }
        break;
      case 2:
        break;
      case 3:
        break;
      case 6:
        break;
      default:
        break;
    }

    return $record;
  }

}
