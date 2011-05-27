<?php

class ProductParameter
{
  protected
    $property = null,
    $value = null
  ;
  
  public function __construct(ProductProperty $productProperty, array $productPropertyRelationArray = array())
  {
    $this->property = $productProperty;
    
    $value = array();
    foreach ($productPropertyRelationArray as $propertyRelation)
    {
      switch ($productProperty->type)
      {
        case 'string': case 'integer': case 'float':
          $value[] = $this->formatValue($productProperty->pattern, $propertyRelation['value'], $propertyRelation['unit']);
          break;
        case 'select':
          $option = ProductPropertyOptionTable::getInstance()->getById($propertyRelation['option_id']);
          $value[] = $option ? $this->formatValue($productProperty->pattern, $option->value, $option->unit) : null;
          break;
        case 'text':
          $value[] = $propertyRelation->value_text;
          break;
      }
    }
    
    $this->value = $productProperty->is_multiple ? $value : $value[0];
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function getName()
  {
    return $this->property->name;
  }

  public function isMultiple()
  {
    return $this->property->is_multiple;
  }

  public function getValue()
  {
    return $this->value;
  }


  protected function formatValue($pattern, $value, $unit = null)
  {
    return strtr($pattern, array('%value%' => $value, '%unit%' => $unit));
  }
}