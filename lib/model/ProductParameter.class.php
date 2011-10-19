<?php

class ProductParameter extends myDoctrineVirtualRecord
{
  protected
    $property = null,
    $value = null,
    $group_id,
    $view_list = false
  ;

  public function __construct(ProductTypePropertyRelation $productTypePropertyRelation, array $productPropertyRelationArray = array())
  {
    $this->property = $productTypePropertyRelation['Property'];
    $this->group_id = $productTypePropertyRelation['group_id'];

    $this->view_list = $productTypePropertyRelation['view_list'];

    $value = array();
    foreach ($productPropertyRelationArray as $propertyRelation)
    {
      switch ($this->property->type)
      {
        case 'string': case 'integer': case 'float':
          $value[] = $this->formatValue($this->property->pattern, $propertyRelation['value'], $this->property->unit);
          break;
        case 'select':
          $option = ProductPropertyOptionTable::getInstance()->getById($propertyRelation['option_id']);
          $value[] = $option ? $this->formatValue($this->property->pattern, $option->value, $option->unit) : null;
          break;
        case 'text':
          $value[] = $propertyRelation->value_text;
          break;
      }
    }

    $this->value = $this->property->is_multiple ? $value : $value[0];
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function getName()
  {
    return (string)$this->property;
  }

  public function getDescription()
  {
    return $this->property->description;
  }

  public function isMultiple()
  {
    return $this->property->is_multiple;
  }

  public function getGroupId()
  {
    return $this->group_id;
  }

  public function getValue()
  {
    return trim(is_array($this->value) ? implode(', ', $this->value) : $this->value);
  }

  public function isViewList()
  {
    return $this->view_list;
  }


  protected function formatValue($pattern, $value, $unit = null)
  {
    return strtr($pattern, array('%value%' => $value, '%unit%' => $unit));
  }
}