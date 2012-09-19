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
    if (!empty($data['type_id']))
      $product->setType(new ProductTypeEntity(array('id' => $data['type_id'])));
    elseif (!empty($data['type']))
      $product->setType(new ProductTypeEntity($data['type']));
    if (!empty($data['category']))
      foreach ($data['category'] as $categoryData)
        $product->addCategory(new ProductCategoryEntity($categoryData));
    if (!empty($data['brand']))
      $product->setBrand(new BrandEntity($data['brand']));
    /** @var $groupMap ProductPropertyGroupEntity[] */
    $groupMap = array();
    if (!empty($data['property_group'])) {
      foreach ($data['property_group'] as $group) {
        $product->addPropertyGroup($pg = new ProductPropertyGroupEntity($group));
        $groupMap[$pg->getId()] = $pg;
      }
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
        if(!empty($prop['option']))
          foreach ($prop['option'] as $option)
            $attr->addOption(new ProductPropertyOptionEntity($option));
        $product->addAttribute($attr);
        if(isset($groupMap[$attr->getGroupId()]))
          $groupMap[$attr->getGroupId()]->addAttribute($attr);
      }
    }
    if (!empty($data['model'])) {
      $model = new ProductModelEntity();
      foreach ($data['model']['property'] as $prop){
        $propEntity = new ProductPropertyEntity($prop);
        if(isset($prop['option'])){
          foreach($prop['option'] as $option){
            $optionEntity = new ProductPropertyOptionEntity($option);
            if(isset($option['product']));
            $optionEntity->setProduct(new ProductEntity($option['product']));
            $propEntity->addOption($optionEntity);
          }
        }
        $model->addProperty($propEntity);
      }
      $product->setModel($model);
    }
    if (!empty($data['state']))
      $product->setState(new ProductStateEntity($data['state']));
    if (!empty($data['line']))
      $product->setLine(new ProductLineEntity($data['line']));
    if (!empty($data['label']))
      foreach ($data['label'] as $label)
        $product->addLabel(new ProductLabelEntity($label));
    if(!empty($data['media']))
      foreach($data['media'] as $media)
        $product->addMedia(new ProductMediaEntity($media));
    if(!empty($data['service']))
      foreach($data['service'] as $service)
        $product->addService(new ProductServiceEntity($service));
    if(!empty($data['kit']))
      foreach($data['kit'] as $kit)
        $product->addKit(new ProductKitEntity($kit));
    if(!empty($data['related']))
      $product->setRelatedIdList($data['related']);
    if(!empty($data['accessories']))
      $product->setAccessoryIdList($data['accessories']);
    if(!empty($data['tag']))
      foreach($data['tag'] as $tag)
        $product->addTag(new ProductTagEntity($tag));

    return $product;
  }

  /**
   * @param $token
   * @param bool $loadDynamic
   * @return ProductEntity|null
   */
  public function getByToken($token, $loadDynamic = false)
  {
    $list = $this->getListFyFilter(array(
      'slug' => (string)$token,
      'geo_id' => (int)RepositoryManager::getRegion()->getDefaultRegionId(),
    ), $loadDynamic);
    return $list ? reset($list) : null;
  }

  /**
   * @param $id
   * @param bool $loadDynamic
   * @return ProductEntity|null
   */
  public function getById($id, $loadDynamic = false)
  {
    $list = $this->getListFyFilter(array(
      'id'     => (int)$id,
      'geo_id' => (int)RepositoryManager::getRegion()->getDefaultRegionId(),
    ), $loadDynamic);
    return $list ? reset($list) : null;
  }

  public function loadRelatedAndAccessories(ProductEntity $product, $loadDynamic = true, $limit = null)
  {
    if($limit){
      $idList = array_slice($product->getAccessoryIdList(), 0, $limit);
      $idList = array_merge($idList, array_slice($product->getRelatedIdList(), 0, $limit));
    }else{
      $idList = $product->getAccessoryIdList() + $product->getRelatedIdList();
    }

    if(!$idList)
      return;

    $idList = array_unique($idList);
    $map = array();
    foreach($this->getListById($idList, $loadDynamic) as $item){
      $map[$item->getId()] = $item;
    }

    $related = array();
    foreach($product->getRelatedIdList() as $id)
      if(isset($map[$id]))
        $related[] = $map[$id];

    $accessory = array();
    foreach($product->getAccessoryIdList() as $id)
      if(isset($map[$id]))
        $accessory[] = $map[$id];

    $product->setRelatedList($related);
    $product->setAccessoryList($accessory);
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
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), $loadDynamic);
  }

  /**
   * Load ProductEntity by barcode from core.
   *
   * @param array $barcodeList
   * @param bool $loadDynamic is load dynamic data
   * @return ProductEntity[]
   */
  public function getListByBarcode(array $barcodeList, $loadDynamic = false)
  {
    if (empty($barcodeList)) return array();

    $result = CoreClient::getInstance()->query('product/get', array(
      'select_type' => 'bar_code',
      'bar_code'    => $barcodeList,
      'geo_id'      => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));

    return array_map(function($item) { return $this->create($item); }, $result);

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
    $this->getListFyFilterAsync($callback, array(
      'id' => $idList,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), $loadDynamic);
  }

  /**
   * Load product data for current product kit
   *
   * @param ProductEntity $product
   * @param bool $loadDynamic
   * @return mixed
   */
  public function loadKit(ProductEntity $product, $loadDynamic = false)
  {
    if(!$product->getKitList())
      return;
    /** @var $map ProductKitEntity[] */
    $map = array();
    foreach($product->getKitList() as $kit){
      $map[$kit->getProductId()] = $kit;
    }
    foreach($this->getListById(array_keys($map), $loadDynamic) as $kitProduct)
    {
      $map[$kitProduct->getId()]->setProduct($kitProduct);
    }
  }

  /**
   * @param ProductRelatedCriteria $criteria
   * @param null $order
   * @return mixed
   * @deprecated
   */
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

  /**
   * @param ProductRelatedCriteria $criteria
   * @param null $order
   * @return mixed
   * @deprecated
   */
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