<?php

class myDoctrineCollection extends Doctrine_Collection
{
  public function toValueArray($value)
  {
    $result = array();
    foreach ($this as $record) {
      $result[] = $record->$value;
    }

    return $result;
  }
}