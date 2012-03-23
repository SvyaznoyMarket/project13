<?php

class ProductCategoryRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = new CoreQuery('product.category.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function getAll($index = null)
  {
    $q = new CoreQuery('product.category.get', array());

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    return new ProductCategoryEntity($data);
  }
}