<?php

class DeliveryTypeRepository
{
  public function create($data)
  {
    $data = array_merge(array(
      'desc'     => '',
      'mode_id'  => null,
      'token'    => null,
      'name'     => '',
      'products' => null,
      'shops'    => null,
    ), $data);

    $entity = new DeliveryTypeEntity();

    $entity->setDescription($data['desc']);
    $entity->setId($data['mode_id']);
    $entity->setToken($data['token']);
    $entity->setName($data['name']);
    $entity->setProduct(
      $data['products']
      ? array_map(function($i) { return RepositoryManager::getProduct()->create(is_array($i) ? $i : array('id' => $i)); }, $data['products'])
      : array()
    );
    $entity->setShop(
      $data['shops']
      ? array_map(function($i) { return RepositoryManager::getShop()->create(is_array($i) ? $i : array('id' => $i)); }, $data['shops'])
      : array()
    );

    return $entity;
  }
}