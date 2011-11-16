<?php

abstract class myDoctrineRecord extends sfDoctrineRecord
{
  protected
    $corePush = true
  ;

  public function postSave($event)
  {
    $invoker = $event->getInvoker();

    $this->deleteResultCache($invoker);

    // Adds keys to nginx file
    CacheEraser::getInstance()->erase($this->getTable()->getCacheEraserKeys($invoker, 'save'));
  }

  public function preDelete($event)
  {
    $invoker = $event->getInvoker();

    $this->deleteResultCache($invoker);

    CacheEraser::getInstance()->erase($this->getTable()->getCacheEraserKeys($invoker, 'delete'));
  }

  public function deleteResultCache($record)
  {
    $prefix = sfConfig::get('app_doctrine_result_cache_prefix', 'dql:');
    $driver = $record->getTable()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
    if ($driver)
    {
      foreach ($this->getTable()->getCacheKeys($record) as $key)
      {
        $driver->deleteByPattern($prefix.$key);
      }
    }
  }

  public function toParams()
  {
    return array(
      $this->getTable()->getQueryRootAlias() => $this->id,
    );
  }

  public function toArray($deep = true, $prefixKey = false)
  {
    if ($this->_state == self::STATE_LOCKED || $this->_state == self::STATE_TLOCKED)
    {
      return false;
    }

    $stateBeforeLock = $this->_state;
    $this->_state = $this->exists() ? self::STATE_LOCKED : self::STATE_TLOCKED;

    $a = array();

    foreach ($this as $column => $value)
    {
      if ($value === self::$_null || is_object($value))
      {
        $value = null;
      }

      $columnValue = $this->get($column, false);

      if ($columnValue instanceof Doctrine_Record)
      {
        $a[$column] = $columnValue->getIncremented();
      }
      else
      {
        $a[$column] = is_object($columnValue) ? $columnValue->toArray() : $columnValue;
      }
    }

    if ($this->_table->getIdentifierType() == Doctrine_Core::IDENTIFIER_AUTOINC)
    {
      $i = $this->_table->getIdentifier();
      $a[$i] = $this->getIncremented();
    }

    if ($deep)
    {
      foreach ($this->_references as $key => $relation)
      {
        if (!$relation instanceof Doctrine_Null)
        {
          $a[$key] = $relation->toArray($deep, $prefixKey);
        }
      }
    }

    // [FIX] Prevent mapped Doctrine_Records from being displayed fully
    foreach ($this->_values as $key => $value)
    {
      $a[$key] = ($value instanceof Doctrine_Record || $value instanceof Doctrine_Collection || $value instanceof myDoctrineVirtualRecord || $value instanceof myDoctrineVirtualCollection) ? $value->toArray($deep, $prefixKey) : $value;
    }

    $this->_state = $stateBeforeLock;

    return $a;
  }

  public function exportToCore()
  {
    $data = array();

    $mapping = $this->getTable()->getCoreMapping();

    if (!is_array($mapping))
    {
      throw new Exception('Method not implemented');
    }

    foreach ($mapping as $k => $v)
    {
      if (is_array($v))
      {
        // checks relation
        if (!empty($v['rel']))
        {
          $table = $this->getTable();
          $relation = $table->getRelation($v['rel']);
          $model = $relation->getTable()->getComponentName();

          $data[$k] = Doctrine_Core::getTable($model)->getCoreIdById($this->get($relation->getLocalFieldName()));
        }
      }
      else {
        $data[$k] = $this->get($v);
      }

    }

    if (!$this->exists() && (isset($data['id']) || empty($data['id'])))
    {
      unset($data['id']);
    }

    return $data;
  }

  public function importFromCore(array $data)
  {
    $mapping = $this->getTable()->getCoreMapping();

    if (!is_array($mapping))
    {
      throw new Exception('Method not implemented');
    }

    foreach ($mapping as $k => $v)
    {
      if (is_array($v))
      {
        // checks relation
        if (!empty($v['rel']))
        {
          $table = $this->getTable();
          $relation = $table->getRelation($v['rel']);
          $model = $relation->getTable()->getComponentName();

          // is relation one to one
          if ($relation->isOneToOne())
          {
            $this->set($relation->getLocalFieldName(), Doctrine_Core::getTable($model)->getIdByCoreId($data[$k]));
          }
          // is relation many to many
          else {
            $existing = $this->get($v['rel'])->getPrimaryKeys();
            $new = array();

            if (isset($data[$k])) foreach ($data[$k] as $d)
            {
              if (!$id = Doctrine_Core::getTable($model)->getIdByCoreId($d['id']))
              {
                throw new Exception('Can\'t find '.$model.' with core_id='.$d['id']);
              }

              $new[] = $id;
            }

            $unlink = array_diff($existing, $new);
            if (count($unlink))
            {
              $this->unlink($v['rel'], $unlink);
            }

            $link = array_diff($new, $existing);
            if (count($link))
            {
              $this->link($v['rel'], $link);
            }
          }
        }
      }
      else {
        $this->set($v, $data[$k]);
      }
    }
  }

  public function setCorePush($value)
  {
    $this->corePush = (boolean)$value;
  }

  public function getCorePush()
  {
    return $this->corePush;
  }

  public function getCacheEraserKeys($action = null)
  {
    return $this->getTable()->getCacheEraserKeys($this, $action);
  }

  // TODO: удалить
  protected function getRecordByCoreId($model, $coreId, $returnId = false)
  {
    return myDoctrineTable::getRecordByCoreId($model, $coreId, $returnId);
  }
}