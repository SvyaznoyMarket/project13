<?php

class ProductLineRepository
{
  /**
   * Load product line
   *
   * @param array $id
   * @return ProductLineEntity
   */
  public function getById($id)
  {
    $data = CoreClient::getInstance()->query('line.list', array(
      'id' => array($id),
      'geo_id' => RepositoryManager::getRegion()->getDefaultRegionId(),
    ));
    $itemData = reset($data);
    return new ProductLineEntity($itemData);
  }

  /**
   * Load product line entity with related products
   *
   * @param $id
   * @return ProductLineEntity
   */
  public function getByIdWithProducts($id)
  {
    $line = $this->getById($id);
    if($line->getMainProductId()) // send bulk request main product id + related product list
    {
      $idList = $line->getProductIdList();
      array_unshift($idList, $line->getMainProductId()); // add main product id as first element
      $productList = RepositoryManager::getProduct()->getListById($idList,true);
      if(isset($productList[0]) && $productList[0]->getId() === $line->getMainProductId())
      {
        // Shift first element from product list as main product
        $product = array_shift($productList);
        // load kit data
        RepositoryManager::getProduct()->loadKit($product);
        $line->setMainProduct($product);
      }
      $line->setProductList($productList);
    }
    else // send simple request with related product list
    {
      $productList = RepositoryManager::getProduct()->getListById($line->getProductIdList(),true);
      $line->setProductList($productList);
    }
    return $line;
  }
}
