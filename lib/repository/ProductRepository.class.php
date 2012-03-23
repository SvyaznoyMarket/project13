<?php

/**
 * ProductEntity repository
 */
class ProductRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    return ProductTable::getInstance()->getListByCoreIds($ids, array('hydrate_array' => true));
  }

  /**
   * @param $data
   * @return ProductEntity
   */
  public function create($data)
  {
    $entity = new ProductEntity($data);
    if(isset($data['type_id'])){
      $type = new ProductTypeEntity(array('id' => $data['type_id']));
      $entity->setType($type);
    }
    if(isset($data['category'])){
      foreach($data['category'] as $categoryData)
      {
        $entity->addCategory(new ProductCategoryEntity($categoryData));
      }
    }
    return $entity;
  }

  /**
   * @param $token
   * @param $regionId
   * @return ProductEntity|null
   */
  public function getByToken($token, $regionId = null)
  {
    if($regionId == null){
      $regionId = RepositoryManager::getRegion()->getDefaultRegionId();
    }
    $list = $this->getListFyFilter(array(
      'slug' => (string)$token,
      'geo_id' => (int)$regionId,
    ), true);
    return $list ? reset($list) : null;
  }

  /**
   * Load ProductEntity by id from core.
   *
   * @param array $idList
   * @param bool $loadDynamic is load dynamic data
   * @return ProductEntity[]
   */
  public function getListById(array $idList, $loadDynamic = false){
    return $this->getListFyFilter(array(
      'id' => $idList,
    ), $loadDynamic);
  }

  public function getRelated(ProductRelatedCriteria $criteria, $order = null)
  {
    $params = array(
      'count'  => 'false',
      'geo_id' => $criteria->getRegion()->core_id,
    );
    $this->applyCriteria($criteria, $params);
    $params['id'] = $criteria->getParent();
    $q = $this->createQuery('product.related.get', $params);
    $result = array_map(function($i) { return $i['id']; }, $q->getResult());
    $this->applyPager($criteria, $q);
    return $this->get($result);
  }

  public function getAccessory(ProductRelatedCriteria $criteria, $order = null)
  {
    $params = array(
      'count'  => 'false',
      'geo_id' => $criteria->getRegion()->core_id,
    );
    $this->applyCriteria($criteria, $params);
    $params['id'] = $criteria->getParent();
    $q = $this->createQuery('product.accessory.get', $params);
    $result = array_map(function($i) { return $i['id']; }, $q->getResult());
    $this->applyPager($criteria, $q);
    return $this->get($result);
  }

  /**
   * @param array $filter
   * @param bool $loadDynamic
   * @return array
   */
  private function getListFyFilter(array $filter, $loadDynamic = false){
    $data = array();
    $callback = function($response) use (&$data){
      if(empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
    };
    $this->coreClient->addQuery('product/get-static',$filter,array(),$callback);
    if($loadDynamic)
      $this->coreClient->addQuery('product/get-dynamic',$filter,array(),$callback);
    $this->coreClient->execute();
    $list = array();
    foreach($data as $item)
      $list[] = $this->create($item);
    return $list;
  }
}