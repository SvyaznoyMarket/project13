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
      'id' => $options['packet_id']
    ));

    foreach ($response as $item)
    {
      $i = 0;
      foreach ($item['data'] as $entity)
      {
        if ($table = $core->getTable($entity['type']))
        {
          if (0 == $i)
          {
            $method = 'prepare'.$table->getComponentName().'Table';
            if (method_exists($this, $method))
            {
              call_user_func_array(array($this, $method), array());
            }
            else {
              $this->connection->exec('TRUNCATE TABLE '.$table->getTableName());
            }
          }
          $i++;

          $this->logSection($table->getComponentName(), $entity['type'].' #'.$entity['id']);

          $method = 'create'.$table->getComponentName().'Record';
          $record =
            method_exists($this, $method)
            ? call_user_func_array(array($this, $method), array($entity['data']))
            : $table->createRecordFromCore($entity['data'])
          ;
          $this->pushRecord($record);
        }
        else {
          $this->logSection('Unknown entity', $entity['type'].' #'.$entity['id'], null, 'ERROR');
        }
      }
      $this->flushCollections();
    }

    $this->connection->exec('SET foreign_key_checks = 1');

    //myDebug::dump($response, false, 'yaml');
  }

  protected function pushRecord(myDoctrineRecord $record)
  {
    $name = $record->getTable()->getComponentName();

    if (!isset($this->collections[$name]))
    {
      $this->collections[$name] = $record->getTable()->createList();
    }

    $this->collections[$name][] = $record;
  }

  protected function flushCollections()
  {
    foreach ($this->collections as $name => $collection)
    {
      $method = 'flush'.$name.'Collection';
      if (method_exists($this, $method))
      {
        call_user_func_array(array($this, $method), array($collection));
      }
      else {
        //$collection->save();
      }

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
    $record->mapValue('parent_core_id', $data['parent_id']);

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
      $record->getNode()->insertAsLastChildof($root);
    }

    // формирует уровни дерева
    for ($level = 1; $level < 5; $level++)
    {
      foreach ($collection as $record)
      {
        $node = $record->getNode();
        if ($node->isRoot()) continue;

        if ($level == $record->level)
        {
          $parent = $record->parent_core_id ? $table->findOneByCoreId($record->parent_core_id) : false;
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

}
