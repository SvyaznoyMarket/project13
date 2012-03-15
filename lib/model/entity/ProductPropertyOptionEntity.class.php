<?php

/**
 * Опция у свойства товара
 */
class ProductPropertyOptionEntity
{
  /* @var integer */
  private $id;

  /* @var ProductPropertyEntity */
  private $property;

  /* @var string */
  private $name;


  public function __construct()
  {
    $this->property = new ProductPropertyEntity();
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setProperty(ProductPropertyEntity $property)
  {
    $this->property = $property;
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
}