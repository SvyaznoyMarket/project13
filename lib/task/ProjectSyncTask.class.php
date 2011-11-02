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
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'core'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('dump', null, sfCommandOption::PARAMETER_NONE, 'Only dump response'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
      new sfCommandOption('packet', null, sfCommandOption::PARAMETER_REQUIRED, 'The packet_id', null),
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
    else {
      $this->task = TaskTable::getInstance()->find($arguments['task_id']);
    }

    if (!$this->task)
    {
      return false;
    }

    $params = $this->task->getContentData();
    if (!$params['packet_id'])
    {
      return false;
    }

    // add your code here

    $this->logSection('core', 'loading packet #'.$params['packet_id']);
    $response = $this->core->query('sync.get', array(
      'id' => $params['packet_id'],
    ));
    ////$response = json_decode(file_get_contents(sfConfig::get('sf_data_dir').'/core/product.json'), true);

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
      foreach ($item['data'] as $packet)
      {
        $action = $this->core->getActions($packet['operation']);

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
          $this->task->setErrorData($e->getMessage());
        }
      }
    }

    if ('run' == $this->task->status)
    {
      $this->task->status = 'success';
    }
    $this->task->save();
  }



  protected function processRecord($action, $record)
  {
    if (!$record instanceof myDoctrineRecord)
    {
      return;
    }

    if (('create' == $action) || ('update' == $action))
    {
      $record->replace(); //$record->save();

      // проверка родителя
      if (!empty ($record->core_parent_id) && $record->getTable()->hasTemplate('NestedSet'))
      {
        $modified = $record->getLastModified();
        if (isset($modified['core_lft']) || isset($modified['core_rgt']))
        {
          $parent = $record->getTable()->getIdByCoreId($record->core_parent_id);
          if ($parent->id != $record->getNode()->getParent()->id)
          {
            $record->getNode()->moveAsFirstChildOf($parent);
          }

          $prevSibling = $record->getTable()->getIdByCoreId($record->core_lft);
          if ($prevSibling && ($prevSibling->id != $parent->id))
          {
            $record->getNode()->moveAsPrevSiblingOf($prevSibling);
          }
        }
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

    $this->log($table->getComponentName().': '.$action.' '.$packet['type'].' ##'.$entity['id']);
    //myDebug::dump($entity);

    $record = $table->getByCoreId($entity['id']);

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

    $record->importFromCore($entity);
    $record->setCorePush(false);
    //myDebug::dump($entity);
    //myDebug::dump($record);

    $this->processRecord($action, $record);

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
      default:
        break;
    }

    $this->processRecord($action, $record);

    return true;
  }
}
