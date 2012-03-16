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

    $q = $this->createQuery('product.category.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function getAll($index = null)
  {
    $q = $this->createQuery('product.category.get', array());

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    $entity = new ProductCategoryEntity();

    $mapping = array(
      'id'              => 'id',
      'is_active'       => 'isActive',
      'is_furniture'    => 'isFurniture',
      'name'            => 'name',
      'media_image'     => 'image',
      'link'            => 'link',
      'token'           => 'token',
      'has_line'        => 'hasLine',
      'product_view_id' => 'productView',
      'position'        => 'position',
      'level'           => 'level',
    );
    foreach ($data as $k => $v) {
      if (!array_key_exists($k, $mapping)) continue;

      $entity->{'set'.ucfirst($mapping[$k])}($v);
    }

    return $entity;
  }
}