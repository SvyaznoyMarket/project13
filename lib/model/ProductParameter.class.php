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
        case 'string':
          $value[] = $propertyRelation->value_string;
          break;
        case 'text':
          $value[] = $propertyRelation->value_text;
          break;
        case 'select':
          $option = ProductPropertyOptionTable::getInstance()->getById($propertyRelation->option_id);
          $value[] = $option ? $option->value : null;
          break;      
      }
    }
    
    $this->value = $productProperty->is_multiple ? $value : $value[0];
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
}