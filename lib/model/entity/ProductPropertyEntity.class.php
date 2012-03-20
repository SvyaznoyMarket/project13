<?php

/**
 * Свойство товара
 */
class ProductPropertyEntity
{
  /* @var integer */
  private $id;

  /* @var boolean */
  private $isMultiple;

  /* @var string */
  private $name;

  /* @var string */
  private $type;

  /* @var string */
  private $unit;

  /* @var string */
  private $hint;


  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setIsMultiple($isMultiple)
  {
    $this->isMultiple = $isMultiple;
  }

  public function isMultiple()
  {
    return $this->isMultiple;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function getType()
  {
    return $this->type;
  }

  public function setUnit($unit)
  {
    $this->unit = $unit;
  }

  public function getUnit()
  {
    return $this->unit;
  }

  public function setHint($hint)
  {
    $this->hint = $hint;
  }

  public function getHint()
  {
    return $this->hint;
  }
}