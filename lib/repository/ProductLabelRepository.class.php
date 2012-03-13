<?php

class ProductLabelRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = $this->createQuery('product.label.get', array('id' => $ids));
    $entities = $this->createList($q->getResult(), $index);

    return $entities;
  }

  public function getAll($index = null)
  {
    $q = $this->createQuery('product.label.get', array());
    $entities = $this->createList($q->getResult(), $index);

    return $entities;
  }

  public function create($data)
  {
    $entity = new ProductLabelEntity();
    $entity->setId($data['id']);
    $entity->setImage($data['media_image']);
    $entity->setName($data['name']);

    return $entity;
  }
}