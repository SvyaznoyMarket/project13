<?php

class ProductCategoryRepository
{
  public function getById($id)
  {
    $query = new CoreQuery('product.category.get', array('id' => $id));
    if($data = reset($query->getResult())){
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

  private function getListByQuery(CoreQuery $query){
    $list = array();
    foreach($query->getResult() as $data){
      $list[] = new ProductCategoryEntity($data);
    }
    return $list;
  }
}