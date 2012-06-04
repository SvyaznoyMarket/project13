<?php

class ProductModelEntity
{
  /** @var ProductPropertyEntity[] */
  private $propertyList = array();

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
