<?php

class PriceTypeRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {

  }

  public function create($data)
  {
    $entity = new RegionEntity();

    $mapping = array(
      'id'         => 'id',
      'is_active'  => 'isActive',
      'is_primary' => 'isPrimary',
    );

    foreach ($data as $k => $v) {
      if (!array_key_exists($k, $mapping)) continue;

      $entity->{'set'.ucfirst($mapping[$k])}($v);
    }

    return $entity;
  }
}