<?php

class ProductRepository extends BaseRepository
{
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

    if (count($result))
    {
      $this->applyPager($criteria, $q);
    }

    // tmp
    //$result = array(34, 1638, 1639, 1640);

    return $this->get($result);
  }
}