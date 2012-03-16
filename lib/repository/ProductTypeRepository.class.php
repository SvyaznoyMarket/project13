<?php

class ProductTypeRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = $this->createQuery('product.type.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function getAll($index = null)
  {
    $q = $this->createQuery('product.type.get', array());

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    $entity = new ProductTypeEntity();

    $mapping = array(
      'id'              => 'id',
      'name'            => 'name',
    );
    foreach ($data as $k => $v) {
      if (!array_key_exists($k, $mapping)) continue;

      $entity->{'set'.ucfirst($mapping[$k])}($v);
    }

    return $entity;
  }
}