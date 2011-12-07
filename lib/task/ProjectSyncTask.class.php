<?php

class ProjectSyncTask extends sfBaseTask
{
  protected
    $core = null,
    $logger = null
  ;

  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('task_id', sfCommandArgument::REQUIRED, 'Task id'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('dump', null, sfCommandOption::PARAMETER_NONE, 'Only dump response'),
      new sfCommandOption('format', null, sfCommandOption::PARAMETER_REQUIRED, 'Dumping format', 'yaml'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
      new sfCommandOption('packet', null, sfCommandOption::PARAMETER_REQUIRED, 'The packet_id', null),
      new sfCommandOption('entity', null, sfCommandOption::PARAMETER_REQUIRED, 'The entity', null),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'sync';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectSync|INFO] task does things.
Call it with:

  [php symfony ProjectSync|INFO]
EOF;

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/sync.log'));
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfConfig::set('sf_logging_enabled', $options['log']);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $this->core = Core::getInstance();

    if ($options['packet'])
    {
      $this->task = new Task();
      $this->task->type = 'project.sync';
      $this->task->setDefaultPriority();
      $this->task->setContentData(array(
        'action'    => 'sync',
        'packet_id' => $options['packet'],
      ));
    }
    else if ($options['entity']) {
      $file = sfConfig::get('sf_data_dir').'/core/'.$options['entity'].'.json';
      if (!is_readable($file))
      {
        $this->logBlock("Can't read file {$file}", 'ERROR');

        return false;
      }

      $response = json_decode(file_get_contents($file), true);
      if (!isset($response[0]['data']))
      {
        $response = array(
          array(
            'data' => array_map(function($item) use ($options) { // классная клосюра :)
              return array(
                'type'      => $options['entity'],
                'operation' => 1,
                'data'      => $item,
              );
            }, $response)
          ),
        );
      }

      $this->task = new Task();
      $this->task->type = 'project.sync';
      $this->task->setDefaultPriority();
      $this->task->setContentData(array(
        'action'    => 'sync',
        'packet_id' => null,
      ));
    }
    else {
      $this->task = TaskTable::getInstance()->find($arguments['task_id']);
    }

    if (!$this->task && !$options['entity'])
    {
      return false;
    }

    $params = $this->task->getContentData();
    if (!$params['packet_id'] && !$options['entity'])
    {
      return false;
    }

    // add your code here

    $this->logSection('core', 'loading packet #'.$params['packet_id']);

    if (!$options['entity'])
    {
      $response = $this->core->query('sync.get', array('id' => $params['packet_id']));
    }

    if ($options['dump'])
    {
      myDebug::dump($response, true, $options['format']);

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
      foreach ($item['data'] as $packet)
      {
        $action = $this->core->getActions($packet['operation']);
        $this->task->setContentData('type', $packet['type']);
        $this->task->setContentData('action', $action);

        try
        {
          $method = 'process'.sfInflector::underscore($packet['type']).'Entity';
          $method = method_exists($this, $method) ? $method : 'processDefaultEntity';

          if (!call_user_func_array(array($this, $method), array($action, $packet)))
          {
            $this->logger->err(sfYaml::dump($packet, 6));

            $this->task->attempt++;
            $this->task->status = 'fail';
            $this->task->setErrorData('Unknown model '.$packet['type']);
          }
        }
        catch (Exception $e)
        {
          $this->logSection($packet['type'], ucfirst($action).' entity #'.$packet['data']['id'].' error: '.$e->getMessage(), null, 'ERROR');
          $this->logger->err("{$e->getMessage()}\n".sfYaml::dump($packet, 6));

          $this->task->attempt++;
          $this->task->status = 'fail';
          $this->task->setErrorData("{$e->getMessage()}\n".sfYaml::dump($packet, 6));
        }
      }
    }

    if ('run' == $this->task->status)
    {
      $this->task->status = 'success';
    }

    $this->task->save();
  }



  protected function processRecord($action, $record, $entity = array())
  {
    if (!$record instanceof myDoctrineRecord)
    {
      return;
    }

    if (('create' == $action) || ('update' == $action))
    {
      $record->replace(); //$record->save();
      if ($record instanceof ProductCategory)
      {
        if (!empty($record->FilterGroup))
          $record->FilterGroup->replace();
      }

      // проверка родителя
      if (!empty ($record->core_parent_id) && $record->getTable()->hasTemplate('NestedSet'))
      {
        $modified = $record->getLastModified();
        // Проверяет, сменился ли родитель
        if (isset($modified['core_parent_id']))
        {
          $newParent = $record->getTable()->getByCoreId($record->core_parent_id);
          $oldParent = $record->getNode()->getParent();

          if (true
            && $newParent
            && (!$oldParent || ($oldParent->id != $newParent->id))
          ) {
            $record->getNode()->moveAsLastChildOf($newParent);
          }
        }
        // Проверяет, изменилась ли позиция относительно соседей
        if (isset($modified['position']))
        {
          $parent = $record->getTable()->getByCoreId($record->core_parent_id);
          if ($parent)
          {
            $childList = $record->getTable()->createQuery()
              ->where('core_parent_id = ?', $parent->core_id)
              ->orderBy('position ASC')
              ->execute()
            ;
            //myDebug::dump($record->getNode()->getParent());
            //myDebug::dump($parent, 1);
            foreach ($childList->toValueArray('id') as $id)
            {
              $parent->refresh();
              $child = $record->getTable()->find($id);
              $child->getNode()->moveAsLastChildOf($parent);
            }
          }
        }
      }

      $method = 'postSave'.$record->getTable()->getComponentName().'Record';
      $method = method_exists($this, $method) ? $method : false;
      if ($method)
      {
        call_user_func_array(array($this, $method), array($record, $entity));
      }
    }
    else if ('delete' == $action)
    {
      if ($record->getTable()->hasTemplate('NestedSet'))
      {
        $record->getNode()->delete();
      }
      else {
        $record->delete();
      }
    }

    $record->free(true);
    $record = null;
    unset($record);
  }



  /**
   *
   * @param string $action
   * @param array $packet
   * @return boolean Success result
   */
  protected function processDefaultEntity($action, $packet)
  {
    $entity = $packet['data'];

    $table = $this->core->getTable($packet['type']);
    if (!$table)
    {
      $this->logSection($packet['type'], "{$action} {$packet['type']} #{$entity['id']}: model doesn't exist. Skip...", null, 'ERROR');
      $this->logger->log('Unknown entity: '.$packet['type']."\n".sfYaml::dump($packet, 6));

      return false;
    }

    if (isset($data['id']))
    {
      $this->log($table->getComponentName().': '.$action.' '.$packet['type'].' ##'.$entity['id']);
      //myDebug::dump($entity);

      $record = $table->getByCoreId($entity['id']);
    }
    else
    {
      $record = false;
    }

    // если действие "создать", но запись с таким core_id уже существует
    if (('create' == $action) && $record)
    {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} #{$record->id} already exists. Force update...", null, 'INFO');
    }
    // если действие "обновить", но запись с таким core_id не существует
    if (('update' == $action) && !$record)
    {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exists. Force create...", null, 'INFO');
    }
    // если действие "удалить", но запись с таким core_id не существует
    if (('delete' == $action) && !$record)
    {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exists. Skip...", null, 'INFO');
    }

    if (!$record)
    {
      $record = $table->create();
    }

    if (('create' == $action) || ('update' == $action))
    {
      $record->importFromCore($entity);
    }
    $record->setCorePush(false);
    //myDebug::dump($entity);
    //myDebug::dump($record);

    $this->processRecord($action, $record, $entity);

    return true;
  }
  /**
   *
   * @param string $action
   * @param array $packet
   * @return boolean Success result
   */
  protected function processUploadEntity($action, $packet)
  {
    $entity = $packet['data'];

    $record = false;
    if (isset($entity['item_type_id'])) switch ($entity['item_type_id'])
    {
      case 1:
        switch ($entity['type_id'])
        {
          case 1:
            $table = ProductPhotoTable::getInstance();
            $record = $table->getByCoreId($entity['id']);
            if (!$record)
            {
              $record = $table->createRecordFromCore($entity);
            }

            $record->importFromCore($entity);
            //$record->product_id = ProductTable::getInstance()->getIdByCoreId($entity['item_id']);
            $record->view_show = 1;
            break;
          case 2:
            $table = ProductPhoto3DTable::getInstance();
            $record = $table->getByCoreId($entity['id']);
            if (!$record)
            {
              $record = $table->createRecordFromCore($entity);
            }

            $record->importFromCore($entity);
            //$record->product_id = ProductTable::getInstance()->getIdByCoreId($entity['item_id']);
            break;
        }
        break;
      case 2:
        break;
      case 3:
        break;
      case 6:
        break;
      case 8:
        $table = ShopPhotoTable::getInstance();
        $record = $table->getByCoreId($entity['id']);
        if (!$record)
        {
          $record = $table->createRecordFromCore($entity);
        }

        $record->importFromCore($entity);
        break;
      default:
        break;
    }

    $this->processRecord($action, $record, $entity);

    return true;
  }



  protected function postSaveProductTypeRecord(ProductType $record, array $entity)
  {
  }
}
