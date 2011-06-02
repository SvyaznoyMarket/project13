<?php

abstract class myDoctrineRecord extends Doctrine_Record
{

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

}