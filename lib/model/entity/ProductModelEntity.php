<?php

class ProductModelEntity
{
  /** @var int[] */
  private $productIdList = array();
  /** @var ProductPropertyEntity[] */
  private $propertyList = array();

  public function setProductIdList($productIdList)
  {
    $this->productIdList = array();
    foreach ($productIdList as $productId)
      $this->addProductId($productId);
  }

  public function addProductId($productId)
  {
    $this->productIdList[] = (int)$productId;
  }

  public function getProductIdList()
  {
    return $this->productIdList;
  }

  public function setPropertyList($propertyList)
  {
    $this->propertyList = array();
    foreach ($propertyList as $property)
      $this->addProperty($property);
  }

  public function addProperty(ProductPropertyEntity $prop)
  {
    $this->propertyList[] = $prop;
  }

  public function getPropertyList()
  {
    return $this->propertyList;
  }

  /**
   * @return string
   */
  public function getVariations()
  {
    $list = array();
    foreach ($this->propertyList as $prop)
      $list[] = mb_strtolower($prop->getName(), mb_detect_encoding($prop->getName()));
    return join(', ', $list);
  }
}
