<?php

class ProductPropertyValue implements ArrayAccess, Iterator, Countable
{
  protected
    $value = null,
    $count = 0
  ;

  public function __construct(ProductProperty $productProperty, array $productPropertyRelation = array())
  {
    $value = array();
    foreach ($productPropertyRelation as $propertyRelation)
    {
      $value[] = $this->getValue($productProperty, $propertyRelation);
    }
    
    $this->value = $productProperty->is_multiple ? $value : $value[0];
  }

  public function __toString()
  {
    return is_array($this->value) ? implode(PHP_EOL, $this->value) : (string)$this->value;
  }
  
  public function get()
  {
    return $this->value;
  }
  
  protected function getValue(ProductProperty $productProperty, ProductPropertyRelation $productPropertyRelation)
  {
    $return = null;

    switch ($productProperty->type)
    {
      case 'string':
        $return = $productPropertyRelation->value_string;
        break;
      case 'text':
        $return = $productPropertyRelation->value_text;
        break;
      case 'select':
        $option = ProductPropertyOptionTable::getInstance()->getById($productPropertyRelation->option_id);
        $return = $option ? $option->value : null;
        break;      
    }
    
    return $return;
  }



  public function offsetExists($offset)
  {
    return $this->value[$offset];
  }

  public function offsetGet($offset) {
    return $this->value[$offset];
  }

  public function offsetSet($offset, $value) {
    throw new LogicException('Cannot update ProductPropertyValue.');
  }

  public function offsetUnset($offset)
  {
    $this->value = null;
  }



  public function rewind()
  {
    reset($this->value);
    $this->count = count($this->value);
  }

  public function key()
  {
    return current($this->value);
  }

  public function current()
  {
    return $this[current($this->value)];
  }

  public function next()
  {
    next($this->value);
    --$this->count;
  }

  public function valid()
  {
    return $this->count > 0;
  }

  public function count()
  {
    return count($this->value);
  }
}