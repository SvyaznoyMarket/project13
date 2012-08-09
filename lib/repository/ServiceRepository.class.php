<?php

class ServiceRepository
{
  /**
   * @param ServiceCategoryEntity[] $categoryList
   */
  public function loadServiceList(array $categoryList){
    /** @var $category ServiceCategoryEntity */
    foreach($categoryList as $category){
      CoreClient::getInstance()->addQuery('service.list', array(
        'category_id' => $category->getId(),
        'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
      ), array(), function($data) use($category){
        /** @var $category ServiceCategoryEntity */
        $category->setServiceIdList($data['list']);
      });
    }
    CoreClient::getInstance()->execute();
    $idList = array();
    foreach($categoryList as $category){
      $idList = array_merge($idList, $category->getServiceIdList());
    }
    if(!empty($idList)){
      $idList = array_unique($idList);
      $result = CoreClient::getInstance()->query('service.get2', array(
        'id' => $idList,
        'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
      ));
      $map = array();
      if (is_array($result))
        foreach ($result as $serviceData)
          $map[$serviceData['id']] = $this->createService($serviceData);
      foreach($categoryList as $category){
        foreach($category->getServiceIdList() as $id){
          if(isset($map[$id])){
            $category->addService($map[$id]);
          }
        }
      }
    }
  }

  /**
   * @param $id
   * @return null|ServiceCategoryEntity
   */
  public function getCategoryById($id){
    $data = CoreClient::getInstance()->query('service.get-category-tree', array(
      'id' => (int)$id,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
    if(empty($data) || !is_array($data)){
      return null;
    }
    return $this->createCategoryEntity((array)$data);
  }

  /**
   * @param string $token
   * @return null|ServiceCategoryEntity
   */
  public function getCategoryByToken($token){
    $data = CoreClient::getInstance()->query('service.get-category-tree', array(
      'id' => (string)$token,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
    if(empty($data) || !is_array($data)){
      return null;
    }
    return $this->createCategoryEntity((array)$data);
  }

  /**
   * @param $id
   * @param null $max_depth
   * @return null|ServiceCategoryEntity
   */
  public function getCategoryTreeById($id, $max_depth=null){
    $params = array(
      'id' => (int)$id,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    );
    if(!is_null($max_depth)){
      $params['max_depth'] = (int)$max_depth;
    }
    $data = CoreClient::getInstance()->query('service.get-category-tree', $params);
    if(empty($data) || !is_array($data)){
      return null;
    }
    return $this->createCategoryEntity((array)$data);
  }

  /**
   * @param $token
   * @param null $max_depth
   * @return null|ServiceCategoryEntity
   */
  public function getCategoryTreeByToken($token, $max_depth=null){
    $params = array(
      'slug' => (string)$token,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    );
    if(!is_null($max_depth)){
      $params['max_depth'] = (int)$max_depth;
    }
    $data = CoreClient::getInstance()->query('service.get-category-tree', $params);
    if(empty($data) || !is_array($data)){
      return null;
    }
    return $this->createCategoryEntity((array)$data);
  }

  public function getCategoryRootTree($max_depth){
    $params = array(
      'max_depth' => (int)$max_depth,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    );
    if(!is_null($max_depth)){
      $params['max_depth'] = (int)$max_depth;
    }
    $data = CoreClient::getInstance()->query('service.get-category-tree', $params);
    if(empty($data) || !is_array($data)){
      return null;
    }
    return $this->createCategoryEntity((array)$data);
  }

  /**
   * @param int[] $idList
   * @return ServiceEntity[]
   */
  public function getListById(array $idList)
  {
    if(empty($idList)){
      return array();
    }
    $result = CoreClient::getInstance()->query('service.get2', array(
      'id' => $idList,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));

    $list = array();
    if (is_array($result))
      foreach ($result as $serviceData)
        $list[] = $this->createService($serviceData);

    return $list;
  }

  /**
   * @param $categoryId
   * @return ServiceEntity[]
   */
  public function getListByCategory($categoryId)
  {
    $categoryList = CoreClient::getInstance()->query('service.list', array(
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
      'category_id' => (int)$categoryId,
    ));
    return $this->getListById((array)$categoryList['list']);
  }

  /**
   * @param string $token
   * @return null|ServiceEntity
   */
  public function getByToken($token)
  {
    $result = CoreClient::getInstance()->query('service.get2', array(
      'slug' => (string)$token,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
    if (empty($result)) {
      return null;
    }
    return $this->createService((array)$result[0]);
  }

  /**
   * @param integer $id
   * @return null|ServiceEntity
   */
  public function getById($id)
  {
    $params = array('id' => $id, 'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId());
    $result = CoreClient::getInstance()->query('service.get', $params);

    if (!$result || !array_key_exists($id, $result)) {
      return null;
    }

    $service = new ServiceEntity($result[$id]);

    return $service;
  }

  /**
   * Load ServiceEntity by id from core.
   *
   * @param $callback
   * @param array $idList
   * @return ServiceEntity[]
   */
  public function getListByIdAsync($callback, array $idList)
  {
    $idList = array_unique($idList);
    if (empty($idList)){
      $callback(array());
      return;
    }

    $cb = function($response) use (&$callback)
    {
      $list = array();
      foreach ($response as $item)
        $list[] = new ServiceEntity($item);
      $callback($list);
    };

    CoreClient::getInstance()->addQuery('service/get2', array(
      'id' => $idList,
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), array(), $cb);
  }

  /**
   * @param array $data
   * @return ServiceEntity
   */
  private function createService(array $data)
  {
    $service = new ServiceEntity($data);

    if (array_key_exists('category_list', $data) && is_array($data['category_list']))
      foreach ($data['category_list'] as $categoryData)
        $service->addCategory($this->createCategoryEntity((array)$categoryData));

    if (array_key_exists('alike_list', $data) && is_array($data['alike_list']))
      foreach ($data['alike_list'] as $alikeId)
        $service->addAlikeId($alikeId);

    return $service;
  }

  /**
   * @param array $data
   * @return ServiceCategoryEntity
   */
  private function createCategoryEntity(array $data)
  {
    $category = new ServiceCategoryEntity($data);

    if (array_key_exists('children', $data) && is_array($data['children']))
      foreach ($data['children'] as $child)
        $category->addChild($this->createCategoryEntity((array)$child));

    if (array_key_exists('parent', $data) && is_array($data['parent']))
      $category->setParent($this->createCategoryEntity($data['parent']));

    return $category;
  }
}
