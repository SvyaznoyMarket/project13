<?php

class ProductTypeRepository  extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = new CoreQuery('product.type.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function getAll($index = null)
  {
    $q = new CoreQuery('product.type.get', array());

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    return new ProductTypeEntity($data);
  }
}