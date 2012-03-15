<?php

class ProductCriteria extends BaseCriteria
{
  protected
    $type = null,
    $brand = null,
    $category = null
  ;

  public function setBrand($brand)
  {
    $this->brand = $brand;
  }

  public function getBrand()
  {
    return $this->brand;
  }

  public function setCategory($category)
  {
    $this->category = $category;

    return $this;
  }

  public function getCategory()
  {
    return $this->category;
  }

  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }

  public function getType()
  {
    return $this->type;
  }
}