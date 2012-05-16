<?php

class ProductCategoryFilterEntity
{
  const TYPE_BOOLEAN = 1;
  const TYPE_DATE = 2;
  const TYPE_NUMBER = 3;
  const TYPE_STRING = 4;
  const TYPE_LIST = 5;
  const TYPE_SLIDER = 6;

  private $filterId;
  private $typeId;
  private $name;
  private $unit;
  private $isMultiple;
  private $isSlider;
  private $min;
  private $max;
  private $optionList = array();

  public function __construct(array $data = array())
  {
    if (array_key_exists('filter_id', $data))   $this->filterId   = (int)$data['filter_id'];
    if (array_key_exists('name', $data))        $this->name       = (string)$data['name'];
    if (array_key_exists('type_id', $data))     $this->typeId     = (int)$data['type_id'];
    if (array_key_exists('unit', $data))        $this->unit       = (string)$data['unit'];
    if (array_key_exists('is_multiple', $data)) $this->isMultiple = (bool)$data['is_multiple'];
    if (array_key_exists('is_slider', $data))   $this->isSlider   = (bool)$data['is_slider'];
    if (array_key_exists('min', $data))         $this->min        = $data['min'];
    if (array_key_exists('max', $data))         $this->max        = $data['max'];
    if (array_key_exists('options', $data))     $this->setOptionList($data['options']);
  }

  public function toArray()
  {
    return array(
      'filter_id' => $this->filterId,
      'name' => $this->name,
      'type_id' => $this->typeId,
      'unit' => $this->unit,
      'is_multiple' => $this->isMultiple,
      'is_slider' => $this->isSlider,
      'min' => $this->min,
      'max' => $this->max,
      'options' => $this->optionList,
    );
  }

  public function setFilterId($filter_id)
  {
    $this->filterId = $filter_id;
  }

  public function getFilterId()
  {
    return $this->filterId;
  }

  public function setIsMultiple($is_multiple)
  {
    $this->isMultiple = (boolean)$is_multiple;
  }

  public function getIsMultiple()
  {
    return $this->isMultiple;
  }

  public function setIsSlider($is_slider)
  {
    $this->isSlider = (boolean)$is_slider;
  }

  public function getIsSlider()
  {
    return $this->isSlider;
  }

  public function setMax($max)
  {
    $this->max = $max;
  }

  public function getMax()
  {
    return $this->max;
  }

  public function setMin($min)
  {
    $this->min = $min;
  }

  public function getMin()
  {
    return $this->min;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setOptionList(array $options)
  {
    $this->optionList = $options;
  }

  public function getOptionList()
  {
    return $this->optionList;
  }

  public function setTypeId($type_id)
  {
    $this->typeId = (int)$type_id;
  }

  public function getTypeId()
  {
    return $this->typeId;
  }

  public function setUnit($unit)
  {
    $this->unit = $unit;
  }

  public function getUnit()
  {
    return $this->unit;
  }
}
