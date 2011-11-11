<?php

class ProductParameter extends myDoctrineVirtualRecord
{
  protected
    $property = null,
    $value = null,
    $group_id,
    $view_list = false,
    $view_show = false
  ;

  public function __construct(ProductTypePropertyRelation $productTypePropertyRelation, array $productPropertyRelationArray = array())
  {
    $this->property = $productTypePropertyRelation['Property'];
    $this->group_id = $productTypePropertyRelation['group_id'];

    $this->view_list = $productTypePropertyRelation['view_list'];
    $this->view_show = $productTypePropertyRelation['view_show'];

    $value = array();
    foreach ($productPropertyRelationArray as $propertyRelation)
    {
      $propertyRelation->mapValue('type', $this->property->type);

      switch ($this->property->type)
      {
        case 'select':
          $option = ProductPropertyOptionTable::getInstance()->getById($propertyRelation['option_id']);
          $value[] = $option ? $this->formatValue($this->property->pattern, $option->value, $option->unit) : null;
          break;
        case 'integer': case 'float':
          $realValue = $propertyRelation['real_value'];
          $frac = $realValue - floor($realValue);
          if (0 == $frac)
          {
            $realValue = intval($realValue);
          }
          else {
            $realValue = rtrim($realValue, '0');
          }

          $realValue = str_replace('.', ',', $realValue);

          $value[] = $this->formatValue($this->property->pattern, $realValue, $this->property->unit);
        case 'boolean':
          $value[] = $this->formatValue($this->property->pattern, $propertyRelation['value'], $this->property->unit);
        default:
          $value[] = $this->formatValue($this->property->pattern, $propertyRelation['real_value'], $this->property->unit);
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

  public function isViewShow()
  {
    return $this->view_show;
  }


  protected function formatValue($pattern, $value, $unit = null)
  {
    return strtr($pattern, array('%value%' => $value, '%unit%' => $unit));
  }
}