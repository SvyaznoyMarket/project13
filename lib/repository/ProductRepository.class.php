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
   * @todo add process all product properties
   */
  public function create($data)
  {
    $product = new ProductEntity($data);
    if (!empty($data['type_id'])) {
      $product->setType(new ProductTypeEntity(array('id' => $data['type_id'])));
    }
    elseif (!empty($data['type'])) {
      $product->setType(new ProductTypeEntity($data['type']));
    }
    if (!empty($data['category'])) {
      foreach ($data['category'] as $categoryData)
      {
        $product->addCategory(new ProductCategoryEntity($categoryData));
      }
    }
    if (!empty($data['brand'])) {
      $product->setBrand(new BrandEntity($data['brand']));
    }
    if (!empty($data['property'])) {
      foreach ($data['property'] as $prop) {
        $attr = new ProductAttributeEntity($prop);
        if (!empty($prop['option_id'])) {
          if (is_array($prop['option_id']))
            foreach ($prop['option_id'] as $option)
              $attr->addOption(new ProductPropertyOptionEntity($option));
          else if (is_numeric($prop['option_id']))
            $attr->setOptionList(array(new ProductPropertyOptionEntity(array('id' => $prop['option_id']))));
        }
        $product->addAttribute($attr);
      }
    }
    if (!empty($data['model'])) {
      $model = new ProductModelEntity();
      $model->setProductIdList($data['model']['product']);
      foreach ($data['model']['property'] as $prop)
        $model->addProperty(new ProductPropertyEntity($prop));
      $product->setModel($model);
    }
    if (!empty($data['state'])) {
      $product->setState(new ProductStateEntity($data['state']));
    }
    if (!empty($data['line'])) {
      $product->setLine(new ProductLineEntity($data['line']));
    }
    if (!empty($data['label'])) {
      foreach ($data['label'] as $label) {
        $product->addLabel(new ProductLabelEntity($label));
      }
    }
    return $product;
  }

  /**
   * @param $token
   * @param $regionId
   * @return ProductEntity|null
   */
  public function getByToken($token, $regionId = null)
  {
    if ($regionId == null) {
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
  public function getListById(array $idList, $loadDynamic = false)
  {
    if (empty($idList))
      return array();
    return $this->getListFyFilter(array(
      'id' => $idList,
    ), $loadDynamic);
  }

  /**
   * Load ProductEntity by id from core.
   *
   * @param $callback
   * @param array $idList
   * @param bool $loadDynamic is load dynamic data
   * @return ProductEntity[]
   */
  public function getListByIdAsync($callback, array $idList, $loadDynamic = false)
  {
    if (empty($idList)){
      $callback(array());
      return;
    }
    $this->getListFyFilterAsync($callback, array('id' => $idList), $loadDynamic);
  }

  public function getRelated(ProductRelatedCriteria $criteria, $order = null)
  {
    $params = array(
      'count' => 'false',
      'geo_id' => $criteria->getRegion()->core_id,
    );
    $this->applyCriteria($criteria, $params);
    $params['id'] = $criteria->getParent();
    $q = new CoreQuery('product.related.get', $params);
    $result = array_map(function($i)
    {
      return $i['id'];
    }, $q->getResult());
    $this->applyPager($criteria, $q);
    return $this->get($result);
  }

  public function getAccessory(ProductRelatedCriteria $criteria, $order = null)
  {
    $params = array(
      'count' => 'false',
      'geo_id' => $criteria->getRegion()->core_id,
    );
    $this->applyCriteria($criteria, $params);
    $params['id'] = $criteria->getParent();
    $q = new CoreQuery('product.accessory.get', $params);
    $result = array_map(function($i)
    {
      return $i['id'];
    }, $q->getResult());
    $this->applyPager($criteria, $q);
    return $this->get($result);
  }

  /**
   * @param $callback
   * @param array $filter
   * @param bool $loadDynamic
   * @return array
   */
  private function getListFyFilterAsync($callback, array $filter, $loadDynamic = false)
  {
    $data = array();
    $self = $this;
    $count = 1;
    $cb = function($response) use (&$self, &$data, &$callback, &$count)
    {
      /** @var $self ProductRepository */
      if (empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach ($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
      $count--;
      if($count === 0)
      {
        $list = array();
        foreach ($data as $item)
          $list[] = $self->create($item);
        $callback($list);
      }
    };
    $this->coreClient->addQuery('product/get-static', $filter, array(), $cb);
    if ($loadDynamic){
      $this->coreClient->addQuery('product/get-dynamic', $filter, array(), $cb);
      $count++;
    }
  }

  /**
   * @param array $filter
   * @param bool $loadDynamic
   * @return array
   */
  private function getListFyFilter(array $filter, $loadDynamic = false)
  {
    $data = array();
    $callback = function($response) use (&$data)
    {
      if (empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach ($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
    };
    $this->coreClient->addQuery('product/get-static', $filter, array(), $callback);
    if ($loadDynamic)
      $this->coreClient->addQuery('product/get-dynamic', $filter, array(), $callback);
    $this->coreClient->execute();
    $list = array();
    foreach ($data as $item)
      $list[] = $this->create($item);
    return $list;
  }

  private function applyCriteria(BaseCriteria $criteria, array &$params)
  {
    if ($pager = $criteria->getPager()) {
      if (null !== $pager->getPage()) {
        $params['start'] = (string)(($pager->getPage() - 1) * $pager->getMaxPerPage());
        $params['limit'] = (string)$pager->getMaxPerPage();
      }
    }
  }

  private function applyPager(BaseCriteria $criteria, CoreQuery $q)
  {
    if ($pager = $criteria->getPager()) {
      $pager->setNbResults($q->count());
    }
  }
}