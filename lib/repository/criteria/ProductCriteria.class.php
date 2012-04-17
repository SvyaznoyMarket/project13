<?php

class ProductCriteria extends BaseCriteria
{
  protected
    $token = null,
    $type = null,
    $brand = null,
    $category = null
  ;

  public function setToken($token)
  {
    $this->token = $token;

    return $this;
  }

  public function getToken()
  {
    return $this->token;
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
}