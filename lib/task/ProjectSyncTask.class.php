<?php

class ProjectSyncTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('task_id', sfCommandArgument::REQUIRED, 'Task id'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'core'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev_green'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('dump', null, sfCommandOption::PARAMETER_NONE, 'Only dump response'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
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
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfConfig::set('sf_logging_enabled', $options['log']);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $this->task = TaskTable::getInstance()->find($arguments['task_id']);
    if ('success' == $this->task->status)
    {
      //return true;
    }

    $params = $this->task->getContentData();
    if (!$params['packet_id'])
    {
      return false;
    }

    // add your code here
    $core = Core::getInstance();

    $this->logSection('core', 'loading packet #'.$params['packet_id']);
    $response = $core->query('sync.get', array(
      'id' => $params['packet_id'],
    ));
    $response = json_decode(file_get_contents(sfConfig::get('sf_data_dir').'/core/product.json'), true);

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
        if ($table = $core->getTable($packet['type']))
        {
          $action = $core->getActions($packet['operation']);

          $entity = $packet['data'];
          $this->log($table->getComponentName().': '.$action.' '.$packet['type'].' ##'.$entity['id']);
          //myDebug::dump($entity);

          try {
            $record = $table->getByCoreId($entity['id']);

            // если действие "создать", но запись с таким core_id уже существует
            if ($record && ('create' == $action))
            {
              $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} #{$record->id} already exists. Force update...", null, 'INFO');
            }
            // если действие "обновить", но запись с таким core_id не существует
            if (!$record && ('update' == $action))
            {
              $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exists. Force create...", null, 'INFO');
            }
            // если действие "удалить", но запись с таким core_id не существует
            if (!$record && ('delete' == $action))
            {
              $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exists. Skip...", null, 'INFO');
            }

            if (!$record)
            {
              $record = $table->create();
            }

            $record->importFromCore($entity);
            $record->setCorePush(false);
            myDebug::dump($entity);
            myDebug::dump($record);
            $record->save();

            $this->task->status = 'success';
            $this->task->save();
          }
          catch (Exception $e) {
            $this->logSection($packet['type'], ucfirst($action).' entity #'.$entity['id'].' error: '.$e->getMessage(), null, 'ERROR');
            $this->task->attempt++;
            $this->task->save();
          }
        }
        // model doesn't exists
        else {
          $this->logSection($packet['type'], "{$action} {$packet['type']} #{$entity['id']}: model doesn't exists. Skip...", null, 'ERROR');
        }
      }
    }
  }
}
