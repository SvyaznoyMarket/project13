<?php

class ShopRepository
{
  public function create($data)
  {
    $data = array_merge(array(
      'id'           => null,
      'slug'         => null,
      'name'         => '',
      'working_time' => '',
      'address'      => '',
      'coord_long'   => null,
      'coord_lat'    => null,
    ), $data);

    $entity = new ShopEntity();

    $entity->setAddress($data['address']);
    $entity->setLatitude($data['coord_lat']);
    $entity->setLongitude($data['coord_long']);
    $entity->setName($data['name']);
    $entity->setRegime($data['working_time']);
    $entity->setToken($data['slug']);

    return $entity;
  }

  public function count() {
    $result = Core::getInstance()->query('shop/get', array(
      'expand' => array(),
      'count'  => 'true',
    ));

    return isset($result['count']) ? $result['count'] : 0;
  }
}