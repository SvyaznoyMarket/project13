<?php

class RegionRepository
{
  /**
   * @param array $ids
   * @return RegionEntity[]
   */
  public function getListById(array $ids)
  {
    if (empty($ids)) return array();
    $q = new CoreQuery('geo.get', array('id' => $ids));
    $list = array();
    foreach ($q->getResult() as $data) {
      $list[] = $this->create($data);
    }
    return $list;
  }

  /**
   * @param $data
   * @return RegionEntity
   */
  private function create($data)
  {
    $entity = new RegionEntity($data);

    // sets parent
    if (!empty($data['parent'])) {
      $entity->setParent(new ProductCategoryEntity($data['parent']));
    }
    elseif (!empty($data['parent_id'])) {
      $entity->setParent(new ProductCategoryEntity(array('id' => $data['parent_id'])));
    }

    // sets price type
    if (!empty($data['price_list'])) {
      $entity->setPriceType(new PriceTypeEntity($data['price_list']));
    }
    elseif (!empty($data['price_list_id'])) {
      $entity->setPriceType(new PriceTypeEntity(array('id' => $data['price_list_id'])));
    }
    return $entity;
  }

  public function getByToken($token)
  {
    $q = new CoreQuery('geo.get', array('slug' => (string)$token));
    if ($data = reset($q->getResult())) {
      return $this->create($data);
    }
    else return null;
  }

  /**
   * Get default region core_id
   * @return int
   */
  public function getDefaultRegionId()
  {
    return sfContext::getInstance()->getUser()->getRegion('core_id');
  }

  public function getById($id)
  {
    $q = new CoreQuery('geo.get', array(
      'id' => (int)$id,
      "count" => false,
      "start" => "",
      "limit" => "",
      "expand" => array("store", "price_list")
    ));
    if ($data = reset($q->getResult())) {
      return $this->create($data);
    }
    else return null;
  }
}