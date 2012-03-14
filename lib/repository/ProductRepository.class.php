<?php

class ProductRepository extends BaseRepository
{
  public function get(array $ids, $index = null)
  {
    return ProductTable::getInstance()->getListByCoreIds($ids, array('hydrate_array' => true));
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
    //myDebug::dump($result);
    //myDebug::dump($q->getErrors());

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
}