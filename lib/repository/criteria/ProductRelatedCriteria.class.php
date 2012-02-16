<?php

class ProductRelatedCriteria extends BaseCriteria
{
  protected
    $parent = null,
    $type = null,
    $brand = null,
    $category = null
  ;

  public function setParent($parent)
  {
    $this->parent = $parent;

    return $this;
  }

  public function getParent()
  {
    return $this->parent;
  }
}