<?php

class myDoctrineVirtualRecord
{

  public function toArray($deep = true, $prefixKey = false)
  {

    $a = array();

    foreach ($this as $key => $value) {
      $a[$key] = ($value instanceof Doctrine_Record || $value instanceof Doctrine_Collection || $value instanceof myDoctrineVirtualRecord || $value instanceof myDoctrineVirtualCollection) ? $value->toArray($deep, $prefixKey) : (is_object($value) && method_exists($value, 'toArray') ? $value->toArray($deep) : $value);
    }

    return $a;
  }

}