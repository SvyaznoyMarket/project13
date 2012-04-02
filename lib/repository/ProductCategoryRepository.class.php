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
      return $this->getListByQuery(new CoreQuery('product.category.get', array('id' => $idList)));
  }

  public function getAll()
  {
    return $this->getListByQuery(new CoreQuery('product.category.get', array()));
  }

  /**
   * @param int $categoryId core category id
   * @param int $maxLevel
   * @param bool $loadParents
   * @return ProductCategoryEntity[]
   */
  public function getChildren($categoryId, $maxLevel = null, $loadParents = false)
  {
    $data = CoreClient::getInstance()->query('category.tree', array(
      'root_id' => $categoryId,
      'max_level' => $maxLevel,
      'is_load_parents' => $loadParents,
    ));
    return $this->fromArray($data);
  }

  private function fromArray(array $categoryDataList)
  {
    $list = array();
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
}