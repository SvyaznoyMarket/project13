<?php

class ProjectSyncTask extends sfBaseTask
{
  protected
    $core = null,
    $logger = null;

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

    $this->namespace = 'project';
    $this->name = 'sync';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectSync|INFO] task does things.
Call it with:

  [php symfony ProjectSync|INFO]
EOF;

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir') . '/sync.log'));
  }

  protected function execute($arguments = array(), $options = array())
  {
    $start_time = microtime(true); //начало работы задачи
    sfConfig::set('sf_logging_enabled', $options['log']);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $this->conn = Doctrine_Manager::connection();

    // add your code here
    $this->core = Core::getInstance();

    $start_reading_time = microtime(true); //начало получения данных
    if ($options['packet']) {
      $this->task = new Task();
      $this->task->type = 'project.sync';
      $this->task->setDefaultPriority();
      $this->task->setContentData(array(
        'action' => 'sync',
        'packet_id' => $options['packet'],
      ));
    }
    else if ($options['entity']) {
      $file = sfConfig::get('sf_data_dir') . '/core/' . $options['entity'] . '.json';
      if (!is_readable($file)) {
        $this->logBlock("Can't read file {$file}", 'ERROR');

        return false;
      }

      $response = json_decode(file_get_contents($file), true);
      if (!isset($response[0]['data'])) {
        $response = array(
          array(
            'data' => array_map(function($item) use ($options)
            { // классная клосюра :)
              return array(
                'type' => $options['entity'],
                'operation' => 1,
                'data' => $item,
              );
            }, $response)
          ),
        );
      }

      $this->task = new Task();
      $this->task->type = 'project.sync';
      $this->task->setDefaultPriority();
      $this->task->setContentData(array(
        'action' => 'sync',
        'packet_id' => null,
      ));
    }
    else {
      $this->task = TaskTable::getInstance()->find($arguments['task_id']);
    }

    if (!$this->task && !$options['entity']) {
      return false;
    }

    //$params = $this->task->getContentData();
    //if (!$params['packet_id'] && !$options['entity'])
    if (!$this->task->core_packet_id && !$options['entity']) {
      return false;
    }

    // add your code here

    //$this->logSection('core', 'loading packet #'.$params['packet_id']);
    $this->logSection('core', 'loading packet #' . $this->task->core_packet_id);

    $this->logSection('timer', 'before sending query to core ' . (microtime(true) - $start_time) . ' sec');
    if (!$options['entity']) {
      //$response = $this->core->query('sync.get', array('id' => $params['packet_id']));
      $response = $this->core->query('sync.get', array('id' => $this->task->core_packet_id));
    }
    $this->logSection('timer', 'reading data: ' . (microtime(true) - $start_reading_time) . ' sec');

    if ($options['dump']) {
      myDebug::dump($response, true, $options['format']);

      return;
    }

    if (!is_array($response)) {
      $this->logSection('core', "error\n" . sfYaml::dump($response));

      return false;
    }

    if (!empty($response['result']) && ('empty' == $response['result'])) {
      $this->logSection('core', 'empty result', null, 'ERROR');

      return;
    }

    $start_processing_time = microtime(true);
    foreach ($response as $item)
    {
      foreach ($item['data'] as $packet)
      {
        $action = $this->core->getActions($packet['operation']);
        //$this->task->setContentData('type', $packet['type']);
        //$this->task->setContentData('action', $action);

        try
        {
          $method = 'process' . sfInflector::underscore($packet['type']) . 'Entity';
          $method = method_exists($this, $method) ? $method : 'processDefaultEntity';

          if (!call_user_func_array(array($this, $method), array($action, $packet))) {
            $this->logger->err(sfYaml::dump($packet, 6));

            $this->task->attempt++;
            $this->task->status = 'fail';
            $this->task->setErrorData('Unknown model ' . $packet['type']);
          }
        }
        catch (Exception $e)
        {
          $this->logSection($packet['type'], ucfirst($action) . ' entity #' . $packet['data']['id'] . ' error: ' . $e->getMessage(), null, 'ERROR');
          $this->logger->err("{$e->getMessage()}\n" . sfYaml::dump($packet, 6));

          $this->task->attempt++;
          $this->task->status = 'fail';
          $this->task->setErrorData("{$e->getMessage()}\n" . sfYaml::dump($packet, 6));
        }
      }
    }
    $this->logSection('timer', 'updating data: ' . (microtime(true) - $start_processing_time) . ' sec');

    if ('run' == $this->task->status || 'async_run' == $this->task->status) {
      $this->task->status = 'success';
    }

    $this->task->save();

    //$end_time = microtime(true);

    $this->logSection('timer', 'total execution time: ' . (microtime(true) - $start_time) . ' sec');
  }


  protected function processRecord($action, $record, $entity = array())
  {
    if (!$record instanceof myDoctrineRecord) {
      return;
    }

    if (('create' == $action) || ('update' == $action)) {
      // проверка родителя
      if (!empty ($record->core_parent_id) && $record->getTable()->hasTemplate('NestedSet')) {
        // косвенно определяет существование записи в бд
        $exists = !empty($record->lft) && !empty($record->rgt);

        $modified = $record->getModified(); // если запись уже сохранялась, то $modified = $record->getLastModified();

        $moved = false;
        // если запись не существует в бд или сменился родитель записи
        if (!$exists || array_key_exists('core_parent_id', $modified)) {
          $newParent = $record->getTable()->getByCoreId($record->core_parent_id);
          $oldParent = $exists ? $record->getNode()->getParent() : false;

          if (true
            && $newParent
            && (!$oldParent || ($oldParent->id != $newParent->id))
          ) {
            if ($exists) {
              $record->getNode()->moveAsLastChildOf($newParent);
              $moved = true;
            }
            else {
              $record->getNode()->insertAsLastChildOf($newParent);
            }
          }
        }
        // Проверяет, изменилась ли позиция относительно соседей
        if (array_key_exists('position', $modified) || $moved) {
          $record->replace();
          $parent = $record->getTable()->getByCoreId($record->core_parent_id);
          if ($parent) {
            $childList = $record->getTable()->createQuery()
              ->where('core_parent_id = ?', $parent->core_id)
              ->orderBy('position ASC')
              ->execute();
            foreach ($childList->toValueArray('id') as $id)
            {
              $parent->refresh();
              $child = $record->getTable()->find($id);
              $child->getNode()->moveAsLastChildOf($parent);
            }
          }
        }
      }

      //если это продукт, являющийся главной моделью, отрубаем внешние ключи, чтобы создать связь ProductModelPropertyRelation
      $is_main_product_model = $record instanceof Product && $record->is_model;
      if ($is_main_product_model) {
        $this->connection->exec('SET foreign_key_checks = 0');
      }

      $record->replace(); //$record->save();

      //если это продукт, являющийся главной моделью, ставим у него модельную ссылку на самого себя и включаем обратно внешние ключи
      if ($is_main_product_model) {
        $record->model_id = $record->id;
        $record->save();
        $this->connection->exec('SET foreign_key_checks = 1');
      }

      if ($record instanceof ProductCategory) {
        if (!empty($record->FilterGroup)) {
          $record->FilterGroup->replace();
        }
      }

      $method = 'postSave' . $record->getTable()->getComponentName() . 'Record';
      $method = method_exists($this, $method) ? $method : false;
      if ($method) {
        call_user_func_array(array($this, $method), array($record, $entity));
      }
    }
    else if ('delete' == $action) {
      if ($record->getTable()->hasTemplate('NestedSet')) {
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
    if (!$table) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} #{$entity['id']}: model doesn't exist. Skip...", null, 'ERROR');
      $this->logger->log('Unknown entity: ' . $packet['type'] . "\n" . sfYaml::dump($packet, 6));

      return false;
    }

    if (isset($entity['id'])) {
      $this->log($table->getComponentName() . ': ' . $action . ' ' . $packet['type'] . ' ##' . $entity['id']);
      //myDebug::dump($entity);

      $record = $table->getByCoreId($entity['id']);
    }
    else
    {
      $record = false;
    }

    // если действие "создать", но запись с таким core_id уже существует
    if (('create' == $action) && $record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} #{$record->id} already exists. Force update...", null, 'INFO');
    }
    // если действие "обновить", но запись с таким core_id не существует
    if (('update' == $action) && !$record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exist. Force create...", null, 'INFO');
    }
    // если действие "удалить", но запись с таким core_id не существует
    if (('delete' == $action) && !$record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: {$table->getComponentName()} doesn't exist. Skip...", null, 'INFO');
    }

    if (!$record) {
      $record = $table->create();
    }

    if (('create' == $action) || ('update' == $action)) {
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

    // подковырка: если действие delete, то угадывам item_type_id
    if ('delete' == $action) {
      foreach (array('ProductPhoto' => 1, 'ProductPhoto3D' => 2, 'ShopPhoto' => 8) as $model => $itemTypeId)
      {
        if ($record = Doctrine_Core::getTable($model)->getByCoreId($entity['id'])) {
          $entity['item_type_id'] = $itemTypeId;
          break;
        }
      }
    }

    if (isset($entity['item_type_id']) && 'delete' != $action) {
      $record = false;
      switch ($entity['item_type_id'])
      {
        case 1:
          switch ($entity['type_id'])
          {
            case 1:
              $table = ProductPhotoTable::getInstance();
              $record = $table->getByCoreId($entity['id']);
              if (!$record) {
                $record = $table->createRecordFromCore($entity);
              }

              $record->importFromCore($entity);
              //$record->product_id = ProductTable::getInstance()->getIdByCoreId($entity['item_id']);
              $record->view_show = 1;
              break;
            case 2:
              $table = ProductPhoto3DTable::getInstance();
              $record = $table->getByCoreId($entity['id']);
              if (!$record) {
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
          if (!$record) {
            $record = $table->createRecordFromCore($entity);
          }

          $record->importFromCore($entity);
          break;
        default:
          break;
      }
    }

    $this->processRecord($action, $record, $entity);

    return true;
  }

  /**
   *
   * @param string $action
   * @param array $packet
   * @return boolean Success result
   */
  protected function processProduct_stateEntity($action, $packet)
  {
    $mapping = array(
      'id' => 'core_id',
      'is_price' => 'is_price',
      'is_shop' => 'is_shop',
      'is_store' => 'is_store',
      'is_supplier' => 'is_supplied',
      'status_id' => 'status_id',
      'is_view_list' => 'view_list',
      'is_view_card' => 'view_show',
      'is_buyable' => 'is_instock',

      'geo_id' => array('table' => 'region', 'field' => 'region_id',),
      'product_id' => array('table' => 'product', 'field' => 'product_id',),
    );
    $entity = $packet['data'];

    $this->log('ProductState: ' . $action . ' ' . $packet['type'] . ' ##' . $entity['id']);

    $sql = "SELECT * FROM `product_state` WHERE `core_id` = ?";
    $record = $this->conn->fetchRow($sql, array($entity['id'],));

    // если действие "создать", но запись с таким core_id уже существует
    if (('create' == $action) && $record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: ProductState #{$record['id']} already exists. Force update...", null, 'INFO');
    }
    // если действие "обновить", но запись с таким core_id не существует
    if (('update' == $action) && !$record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: ProductState doesn't exist. Force create...", null, 'INFO');
    }
    // если действие "удалить", но запись с таким core_id не существует
    if (('delete' == $action) && !$record) {
      $this->logSection($packet['type'], "{$action} {$packet['type']} ##{$entity['id']}: ProductState doesn't exist. Skip...", null, 'INFO');
    }

    if ('delete' == $action) {
      $sql = "DELETE * FROM `product_state` WHERE `id` = ?";
      $this->conn->execute($sql, array($entity['id'],));
    }

    if ($record && ('delete' != $action)) //если есть такая запись, то будем ее изменять
    {
      $processed['to_update'] = array();
      $processed = $this->updateRecord($mapping, $record, $entity);

      //      if ($record['is_instock'] != ($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier']))
      //      {
      //        $processed['to_update']['is_instock'] = (int)($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier']);
      //      }
      //карточка товара доступна всегда по прямой ссылке
      //      if ((boolean)$record['view_show'] != $entity['status_id'] >= 1)
      //      {
      //        $processed['to_update']['view_show'] = (int)($entity['status_id'] >= 1);
      //      }
      //в списке товара показывать только если есть картинка и цена
      //      if ((boolean)$record['view_list'] != (($entity['status_id'] >= 2) && $entity['is_image'] && $entity['is_price'] && ($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier'])))
      //      {
      //        $processed['to_update']['view_list'] = (int)(($entity['status_id'] >= 2) && $entity['is_image'] && $entity['is_price'] && ($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier']));
      //      }

      if (count($processed['to_update'])) {
        $fields = array();
        foreach ($processed['to_update'] as $field => $value)
        {
          $fields[] = '`' . $field . '` = ' . (is_null($value) ? 'NULL' : $value);
        }

        $sql = 'UPDATE `product_state` SET ' . implode(', ', $fields) . ' WHERE `id` = ?';
        $this->conn->execute($sql, array($record['id'],));

        //тут очищаем кеш
      }
      if (isset($processed['relation']['region']) && $processed['relation']['region']['is_default']) {
        $products_fields_to_update = array_intersect(array_keys($processed['to_update']), array('view_list', 'view_show', 'status_id', 'is_instock',));
        if (count($products_fields_to_update)) {
          $fields = array();
          foreach ($products_fields_to_update as $field)
          {
            $fields[] = '`' . $field . '` = ' . (is_null($processed['to_update'][$field]) ? 'NULL' : $processed['to_update'][$field]);
          }
          $fields[] = '`updated_at` = NOW()';

          $sql = 'UPDATE `product` SET ' . implode(', ', $fields) . ' WHERE `id` = ?';
          $this->conn->execute($sql, array($processed['relation']['product']['id'],));
        }
      }
    }
    else //если записи нет, то создадим новую
    {
      $processed = $this->updateRecord($mapping, array(), $entity);

      //      $processed['to_update']['is_instock'] = (int)($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier']);
      //      $processed['to_update']['view_show'] = (int)($entity['status_id'] >= 1);
      //      $processed['to_update']['view_list'] = (int)(($entity['status_id'] >= 2) && $entity['is_image'] && $entity['is_price'] && ($entity['is_shop'] || $entity['is_store'] || $entity['is_supplier']));

      $fields = array();
      foreach ($processed['to_update'] as $field => $value)
      {
        $fields['`' . $field . '`'] = is_null($value) ? 'NULL' : $value;
      }

      $record = $processed['to_update'];
      $record['id'] = 'new';
      $sql = 'INSERT INTO `product_state` (' . implode(', ', array_keys($fields)) . ') VALUES (' . implode(', ', $fields) . ')';
      $this->conn->execute($sql);

      if (isset($processed['relation']['region']) && $processed['relation']['region']['is_default']) {
        $products_fields_to_update = array_intersect(array_keys($processed['to_update']), array('view_list', 'view_show', 'status_id', 'is_instock',));
        if (count($products_fields_to_update)) {
          $fields = array();
          foreach ($products_fields_to_update as $field)
          {
            $fields[] = '`' . $field . '` = ' . (is_null($processed['to_update'][$field]) ? 'NULL' : $processed['to_update'][$field]);
          }
          $fields[] = '`updated_at` = NOW()';

          $sql = 'UPDATE `product` SET ' . implode(', ', $fields) . ' WHERE `id` = ?';
          $this->conn->execute($sql, array($processed['relation']['product']['id'],));
        }
      }

    }

    //если изменились параметры отображения, или запись была удалена, то надо сбросить и кэш
    if ('delete' == $action || (count(array_intersect(array_keys($processed['to_update']), array('view_list', 'view_show', 'is_instock',))))) {
      //очищаю redis кэш для товара
      //myCache::getInstance()->removeByTag(ProductStateTable::getInstance()->getCacheTags($record));

      //очищаю nginx кэш
      CacheEraser::getInstance()->erase(array("product-{$processed['relation']['product']['core_id']}-{$processed['relation']['region']['core_id']}"), false, 'product_state ' . (('delete' == $action) ? 'deleted' : 'action'));
    }

    return true;

  }

  protected function updateRecord(array $mapping = array(), array $record = array(), array $data = array())
  {
    if (empty($mapping) || empty($data)) {
      return false;
    }

    $to_update = array();
    $relation = array();

    foreach ($mapping as $k => $v)
    {
      if (is_array($v)) //если это свзяь
      {
        $relationData = $this->getRelationData($v['table'], $data[$k]);
        if (!$relationData) {
          throw new Exception('Unable to find match for entity ' . $v['table'] . ' ##' . $data[$k]);
        }
        if (!isset($record[$v['field']]) || $record[$v['field']] != $relationData['id']) {
          $to_update[$v['field']] = $relationData['id'];
        }

        $relation[$v['table']] = $relationData;
      }
      else //если простое сопоставление полей
      {
        if (!isset($record[$v]) || $record[$v] != $data[$k]) {
          $to_update[$v] = $data[$k];
        }
      }
    }

    //возвращаю массив, содержащий поля для обновления и
    return array(
      'to_update' => $to_update,
      'relation' => $relation,
    );
  }

  protected function getRelationData($table = '', $core_id = null)
  {
    if (empty($table) || empty($core_id)) {
      return false;
    }

    $sql = 'SELECT * FROM `' . $table . '` WHERE `core_id` =?';
    return $this->conn->fetchRow($sql, array($core_id,));
  }

  protected function postSaveProductTypeRecord(ProductType $record, array $entity)
  {
  }
}
