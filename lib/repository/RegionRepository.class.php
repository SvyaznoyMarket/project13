<?php

class RegionRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    $entities = array();

    if (!count($ids))
    {
      return $entities;
    }

    $q = $this->createQuery('geo.get', array('id' => $ids));

    return $this->createList($q->getResult(), $index);
  }

  public function create($data)
  {
    $entity = new RegionEntity();

    $mapping = array(
      'id'        => 'id',
      'is_active' => 'isActive',
      'name'      => 'name',
      'level'     => 'level',
      'lft'       => 'lft',
      'rgt'       => 'rgt',
    );

    foreach ($data as $k => $v) {
      if (!array_key_exists($k, $mapping)) continue;

      $entity->{'set'.ucfirst($mapping[$k])}($v);
    }

    // sets parent
    $entity->setType($this->getRepository('ProductCategory')->create(array_key_exists('parent', $data) ? $data['parent'] : array('id' => $data['parent_id'])));

    // sets price type
    $entity->setPriceType($this->getRepository('PriceType')->create(array_key_exists('price_list', $data) ? $data['price_list'] : array('id' => $data['price_list_id'])));

    return $entity;
  }

  public function getOneByToken(RegionCriteria $criteria)
  {
    $q = $this->createQuery('geo.get', array('slug' => $criteria->getToken()));

    return $this->create(array_shift($q->getResult()));
  }
}