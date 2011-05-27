<?php

class ProductParameterGroup
{
  protected
    $name = null,
    $parameter = null
  ;
  
  public function __construct(ProductPropertyGroup $productPropertyGroup, array $productParameterArray = null)
  {
    $this->name = $productPropertyGroup['name'];

    $this->parameter = $productParameterArray;
    if (empty($this->parameter))
    {
      $this->parameter = new myDoctrineVirtualCollection();
    }
  }

  public function getName()
  {
    return $this->name;
  }

  public function getParameter()
  {
    return $this->parameter;
  }
}