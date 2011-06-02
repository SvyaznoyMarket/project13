<?php

class myDoctrineCollection extends Doctrine_Collection
{
  protected $indexes = array();

  public function toValueArray($value)
  {
    $result = array();
    foreach ($this as $record) {
      $result[] = $record->$value;
    }

    return $result;
  }

  public function indexBy($index)
  {
    if (!$this->getTable()->hasColumn($index))
    {
      throw new Exception('Column '.$index.' does not exists in table '.$this->getTable()->getComponentName());
    }

    $this->indexes[$index] = array();
    foreach ($this as $record)
    {
      $this->indexes[$index][$record->get($index)] = $record;
    }
  }

  public function getByIndex($index, $value)
  {
    return isset($this->indexes[$index][$value]) ? $this->indexes[$index][$value] : null;
  }
}