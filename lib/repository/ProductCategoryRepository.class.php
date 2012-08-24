<?php

class ProductCategoryRepository
{
  public function getById($id)
  {
    $query = new CoreQuery('product.category.get', array('id' => $id));
    if ($data = reset($query->getResult())) {
      return new ProductCategoryEntity($data);
    }
    else
      return null;
  }

  public function getListById(array $idList)
  {
    if (!count($idList))
      return array();
    else
      return $this->getListByQuery(new CoreQuery('product.category.get', array('id' => $idList, 'expand' => array())));
  }

  public function getAll()
  {
    return $this->getListByQuery(new CoreQuery('product.category.get', array()));
  }

  /**
   * @param string $token      Токен категории
   * @param bool   $loadBranch Если true, загружает всех предков (ancestors) и собственных детей (children)
   * @return ProductCategoryEntity
   */
  public function getByToken($token, $loadBranch = false)
  {
    $data = CoreClient::getInstance()->query('category.token', array(
      'token_list' => array($token),
      'region_id'  => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
    $list = $this->fromArray($data);
    /** @var $entity ProductCategoryEntity */
    $entity = reset($list);

    if ($entity && $loadBranch) {
      $this->loadBranch($entity);
    }

    return $entity;
  }

  /**
   * @param string[] $tokenList
   * @return ProductCategoryEntity[]
   */
  public function getListByToken(array $tokenList)
  {
    $data = CoreClient::getInstance()->query('category.token', array(
      'token_list' => $tokenList,
      'region_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));

    return $this->fromArray($data);
  }

  /**
   * @param int $categoryId core category id
   * @param int $maxLevel
   * @param bool $loadParents
   * @return ProductCategoryEntity[]
   */
  public function getTree($categoryId, $maxLevel = null, $loadParents = false)
  {
    $data = CoreClient::getInstance()->query('category.tree', array(
      'root_id' => $categoryId,
      'max_level' => $maxLevel,
      'is_load_parents' => $loadParents,
      'region_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));

    return $this->fromArray($data);
  }

  /**
   * @param callback $callback
   * @param int $categoryId core category id
   * @param int $maxLevel
   * @param bool $loadParents
   */
  public function getTreeAsync($callback, $categoryId, $maxLevel = null, $loadParents = false)
  {
    $self = $this;
    CoreClient::getInstance()->addQuery('category.tree', array(
      'root_id' => $categoryId,
      'max_level' => $maxLevel,
      'is_load_parents' => $loadParents,
      'region_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ), array(), function($data) use($self, $callback){
      /** @var $self ProductCategoryRepository */
      $callback($self->fromArray($data));
    });
  }

  public function fromArray(array $categoryDataList)
  {
    $list = array();
    if(is_null($categoryDataList) || count($categoryDataList) == 0){
      return $list;
    }
    foreach ($categoryDataList as $categoryData)
    {
      $list[] = $item = new ProductCategoryEntity($categoryData);
      if (isset($categoryData['children'])) {
        $item->setChildren($this->fromArray($categoryData['children']));
      }
    }
    return $list;
  }

  private function getListByQuery(CoreQuery $query)
  {
    $list = array();
    foreach ($query->getResult() as $data) {
      $list[] = new ProductCategoryEntity($data);
    }
    return $list;
  }

  private function loadBranch(ProductCategoryEntity $entity) {
    $data = CoreClient::getInstance()->query('category.tree', array(
      'root_id'         => $entity->getId(),
      'max_level'       => null,
      'is_load_parents' => true,
      'region_id'       => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));

    $self = $this;

    $ancestors = array();
    $loadBranch = function($data) use(&$loadBranch, &$ancestors, $self, $entity) {
      /** @var $entity ProductCategoryEntity */
      foreach ($data as $item) {
        // если наткнулись на текущую категорию, то закругляемся...
        if ($entity->getId() == $item['id']) {
          $children = isset($item['children']) ? $self->fromArray((array)$item['children']) : array();

          $entity->setChildren($children);

          return;
        }
      }

      $list = $self->fromArray($data);
      $ancestors[] = reset($list);

      $loadBranch($data[0]['children']);
    };
    $loadBranch($data);

    $entity->setAncestors($ancestors);
  }
}