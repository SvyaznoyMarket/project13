<?php

class ProductLabelCriteria extends BaseCriteria
{
  protected
    $category = null
  ;

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