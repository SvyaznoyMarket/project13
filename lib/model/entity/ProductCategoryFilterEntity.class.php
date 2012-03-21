<?php

class ProductCategoryFilterEntity
{
  const TYPE_BOOLEAN = 1;
  const TYPE_DATE = 2;
  const TYPE_NUMBER = 3;
  const TYPE_STRING = 4;
  const TYPE_LIST = 5;
  const TYPE_SLIDER = 6;

  private $filter_id;
  private $type_id;
  private $name;
  private $unit;
  private $is_multiple;
  private $is_slider;
  private $min;
  private $max;
  private $options = array();

  public function __construct(array $data = array())
  {
    if (array_key_exists('filter_id', $data)) $this->setFilterId($data['filter_id']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
    if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
    if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
    if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
    if (array_key_exists('is_slider', $data)) $this->setIsSlider($data['is_slider']);
    if (array_key_exists('min', $data)) $this->setMin($data['min']);
    if (array_key_exists('max', $data)) $this->setMax($data['max']);
    if (array_key_exists('options', $data)) $this->setOptions($data['options']);
  }

  public function toArray()
  {
    return array(
      'filter_id' => $this->filter_id,
      'name' => $this->name,
      'type_id' => $this->type_id,
      'unit' => $this->unit,
      'is_multiple' => $this->is_multiple,
      'is_slider' => $this->is_slider,
      'min' => $this->min,
      'max' => $this->max,
      'options' => $this->options,
    );
  }

  public function setFilterId($filter_id)
  {
    $this->filter_id = $filter_id;
  }

  public function getFilterId()
  {
    return $this->filter_id;
  }

  public function setIsMultiple($is_multiple)
  {
    $this->is_multiple = (boolean)$is_multiple;
  }

  public function getIsMultiple()
  {
    return $this->is_multiple;
  }

  public function setIsSlider($is_slider)
  {
    $this->is_slider = (boolean)$is_slider;
  }

  public function getIsSlider()
  {
    return $this->is_slider;
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

  public function setOptions(array $options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setTypeId($type_id)
  {
    $this->type_id = (int)$type_id;
  }

  public function getTypeId()
  {
    return $this->type_id;
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
