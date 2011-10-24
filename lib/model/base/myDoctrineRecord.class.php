<?php

abstract class myDoctrineRecord extends sfDoctrineRecord
{
  protected
    $corePush = true;

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
        if (!empty($v['name']) && !empty($v['rel']))
        {
          $model = $this->getTable()->getRelation($v['rel'])->getTable()->getComponentName();
          $data[$k] = Doctrine_Core::getTable($model)->getCoreIdById($this->get($v['name']));
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
        if (!empty($v['name']) && !empty($v['rel']))
        {
          $model = $this->getTable()->getRelation($v['rel'])->getTable()->getComponentName();
          $this->set($v['name'], Doctrine_Core::getTable($model)->getIdByCoreId($data[$k]));
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

  protected function getRecordByCoreId($model, $coreId, $returnId = false)
  {
    return myDoctrineTable::getRecordByCoreId($model, $coreId, $returnId);
  }
}