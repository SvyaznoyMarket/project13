<?php

/**
 * Атрибут товара
 */
class ProductAttributeEntity
{
  /* @var ProductEntity */
  private $product;

  /* @var ProductPropertyEntity */
  private $property;

  /* @var ProductPropertyOptionEntity|null */
  private $option = null;

  /* @var mixed */
  private $value;

  /* @var string */
  private $displayValue;


  public function __construct()
  {
    $this->product = new ProductEntity();
    $this->property = new ProductPropertyEntity();
  }

  public function setProduct(ProductEntity $product)
  {
    $this->product = $product;
  }

  public function getProduct()
  {
    return $this->product;
  }

  public function setProperty(ProductPropertyEntity $property)
  {
    $this->property = $property;
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function setOption(ProductPropertyOption $option)
  {
    $this->option = $option;
  }

  public function getOption()
  {
    return $this->option;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setDisplayValue($displayValue)
  {
    $this->displayValue = $displayValue;
  }

  public function getDisplayValue()
  {
    return $this->displayValue;
  }
}
