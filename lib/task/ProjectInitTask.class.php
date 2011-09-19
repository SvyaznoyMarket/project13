<?php

class ProjectInitTask extends sfBaseTask
{
  protected
    $connection = null,
    $collections = array()
  ;

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
      new sfCommandOption('packet_id', null, sfCommandOption::PARAMETER_REQUIRED, 'Packet id'),
      new sfCommandOption('sync_id', null, sfCommandOption::PARAMETER_REQUIRED, 'Sync id'),
      new sfCommandOption('status', null, sfCommandOption::PARAMETER_REQUIRED, 'Status'),
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
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $this->connection->exec('SET foreign_key_checks = 0');

    // add your code here
    $core = Core::getInstance();

    //myDebug::dump($options);
    $response = $core->query('load.get', array(
      'id' => $options['packet_id'],
    ));

    foreach ($response as $item)
    {
      foreach ($item['data'] as $entity)
      {
        if ($table = $core->getTable($entity['type']))
        {
          $this->log($table->getComponentName().' -> '.$entity['type'].' #'.$entity['id']);

          try {
            $method = 'create'.$table->getComponentName().'Record';
            $record =
              method_exists($this, $method)
              ? call_user_func_array(array($this, $method), array($entity['data']))
              : $table->createRecordFromCore($entity['data'])
            ;
            $this->pushRecord($record);
          }
          catch (Exception $e) {
            $this->logSection('Import entity', $entity['type'].' #'.$entity['id'], null, 'ERROR');
          }
        }
        else {
          $this->logSection('Unknown entity', $entity['type'].' #'.$entity['id'], null, 'ERROR');
        }
      }
    }

    $this->flushCollections();

    $this->connection->exec('SET foreign_key_checks = 1');

    //myDebug::dump($response, false, 'yaml');
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
    $this->logSection('collection', 'flush...');
    foreach ($this->collections as $name => $collection)
    {
      $this->logSection($name, 'prepare...');
      $method = 'prepare'.$name.'Table';
      if (method_exists($this, $method))
      {
        call_user_func_array(array($this, $method), array());
      }
      else {
        $this->connection->exec('TRUNCATE TABLE '.Doctrine_Core::getTable($name)->getTableName());
      }

      $this->logSection($name, 'flush...');
      $method = 'flush'.$name.'Collection';
      if (method_exists($this, $method))
      {
        //if ('ProductCategory' == $name)          myDebug::dump($collection, 1);
        call_user_func_array(array($this, $method), array($collection));
      }
      else {
        $collection->save();
      }

      $this->log('.....'.$collection->count());

      $collection->free();
      $collection = null;
      unset($this->collections[$name]);
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
    $record->mapValue('core_parent_id', $data['parent_id']);

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
    for ($level = 1; $level <= 5; $level++)
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
  protected function createProductCategoryRecord(array $data)
  {
    $record = ProductCategoryTable::getInstance()->createRecordFromCore($data);
    $record->mapValue('core_parent_id', $data['parent_id']);
    $record->token = uniqid().'-'.myToolkit::urlize($record->name);

    return $record;
  }
  // ProductCategory
  protected function flushProductCategoryCollection(myDoctrineCollection $collection)
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

    foreach ($collection as $record)
    {
      if ($record->getNode()->isRoot()) continue;
      $record->refresh();
      $record->save();

      foreach ($tree->fetchRoots() as $root)
      {
        if ($record->core_parent_id == $root->core_id)
        {
          $record->save();
          $record->getNode()->insertAsLastChildOf($root);
        }
      }
    }

    // формирует уровни дерева
    for ($level = 0; $level <= 6; $level++)
    {
      foreach ($collection as $record)
      {
        $record->refresh();
        if (!is_null($level) && ($level == intval($record->level)))
        {
          // ищет прямых потомков
          foreach ($collection as $child)
          {
            if ($child->core_parent_id == $record->core_id)
            {
              $child->save();
              $child->getNode()->moveAsLastChildOf($record);
            }
          }
        }
      }
    }
  }
}
