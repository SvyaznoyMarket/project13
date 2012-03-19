<?php

class ProductRepository extends ObjectRepository
{
  public function get(array $ids, $index = null)
  {
    return ProductTable::getInstance()->getListByCoreIds($ids, array('hydrate_array' => true));
  }

  public function create($data)
  {
    myDebug::dump($data, 1);
    $entity = new ProductEntity();

    $mapping = array(
      'id'           => 'id',
      'is_model'     => 'isModel',
      'score'        => 'score',
      'name'         => 'name',
      'prefix'       => 'prefix',
      'article'      => 'article',
      'bar_code'     => 'barcode',
      'tagline'      => 'tagline',
      'announce'     => 'announce',
      'description'  => 'description',
      'media_image'  => 'defaultImage',
      'rating'       => 'rating',
      'rating_count' => 'ratingQuantity',
      'view_id'      => 'view',
    );

    foreach ($data as $k => $v) {
      if (!array_key_exists($k, $mapping)) continue;

      $entity->{'set'.ucfirst($mapping[$k])}($v);
    }

    // sets productType
    $entity->setType($this->getRepository('ProductType')->create(array_key_exists('type', $data) ? $data['type'] : array('id' => $data['type_id'])));

    // sets categories
    $entity->setCategory($this->getRepository('ProductCategory')->createList($data['category']));

    return $entity;
  }

  public function getOneByToken(ProductCriteria $criteria)
  {
    $resultStatic = $this->coreClient->query('product/get-static', array(
      'slug' => $criteria->getToken()
    ));

    $resultDynamic = $this->coreClient->query('product/get-dynamic', array(
      'slug'   => $criteria->getToken(),
      'geo_id' => $criteria->getRegion()->getId(),
    ));

    $result = array_merge($resultStatic, $resultDynamic);
    myDebug::dump($resultStatic);
    myDebug::dump($result, 1);

    return $result ? $this->create(array_shift($result)) : null;
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