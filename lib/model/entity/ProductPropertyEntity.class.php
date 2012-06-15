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
  /** @var boolean */
  private $isImage;
  /** @var ProductPropertyOptionEntity[] */
  private $optionList = array();

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))          $this->id         = (int)$data['id'];
    if (array_key_exists('is_multiple', $data)) $this->isMultiple = (bool)$data['is_multiple'];
    if (array_key_exists('name', $data))        $this->name       = (string)$data['name'];
    if (array_key_exists('type', $data))        $this->type       = (string)$data['type'];
    if (array_key_exists('unit', $data))        $this->unit       = (string)$data['unit'];
    if (array_key_exists('hint', $data))        $this->hint       = (string)$data['hint'];
    if (array_key_exists('is_image', $data))    $this->isImage    = (boolean)$data['is_image'];
  }


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

  /**
   * @param boolean $isImage
   */
  public function setIsImage($isImage)
  {
    $this->isImage = (boolean)$isImage;
  }

  /**
   * @return boolean
   */
  public function getIsImage()
  {
    return $this->isImage;
  }

  /**
   * @param ProductPropertyOptionEntity[] $optionList
   */
  public function setOptionList($optionList)
  {
    $this->optionList = array();
    foreach($optionList as $option)
      $this->addOption($option);
  }

  public function addOption(ProductPropertyOptionEntity $option)
  {
    $this->optionList[] = $option;
  }

  /**
   * @return ProductPropertyOptionEntity[]
   */
  public function getOptionList()
  {
    return $this->optionList;
  }
}