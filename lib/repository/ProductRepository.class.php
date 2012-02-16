<?php

class ProductRepository extends BaseRepository
{
  public function get(array $ids)
  {
    return ProductTable::getInstance()->getById($ids, array('hydrate_array' => true));
  }

  public function getAccesories(ProductRelatedCriteria $criteria, $order = null)
  {
    $params = array(
      'count' => 'false',
    );

    $this->applyCriteria($criteria, $params);

    $params['id'] = $criteria->getParent();

    $result = $this->getCoreResult('product.accessory.get', $params);
    if (!$result)
    {
      $result = array();
    }

    if (count($result))
    {
      $params['count'] = 'true';
      $nbResult = $this->getCoreResult('product.accessory.get', $params);
      $this->initPager($criteria, $nbResult['count']);
    }

    // tmp
    $result = array(34, 1638, 1639, 1640);

    return $this->get($result);
  }
}