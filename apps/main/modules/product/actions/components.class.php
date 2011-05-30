<?php

/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productComponents extends myComponents
{
 /**
  * Executes show component
  * 
  * @param Product $product Товар
  */
  public function executeShow()
  {
    
  }
 /**
  * Executes list component
  *
  * @param Doctrine_Collection $productList Коллекция товаров
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->productList as $product)
    {
      $list[] = array(
        'name'     => (string)$product,
        'has_link' => $product['view_show'],
        'product'  => $product,
      );
    }
    
    $this->setVar('list', $list, true);
  }
 /**
  * Executes property component
  *
  * @param Product $product Товар
  */
  public function executeProperty()
  {
    $list = array();
    foreach ($this->product['Parameter'] as $parameter)
    {
      $list[] = array(
        'name'  => $parameter->getName(),
        'value' => $parameter->getValue(),
      );
    }
    
    $this->setVar('list', $list, true);
  }
 /**
  * Executes property_grouped component
  *
  * @param Product $product Товар
  */
  public function executeProperty_grouped()
  {
    $list = array();
    foreach ($this->product['ParameterGroup'] as $parameterGroup)
    {
      $parameters = array();
      foreach ($parameterGroup->getParameter() as $parameter)
      {
        $parameters[] = array(
          'name'  => $parameter->getName(),
          'value' => $parameter->getValue(),
        );        
      }
      
      $list[] = array(
        'name'       => $parameterGroup->getName(),
        'parameters' => $parameters,
      );
    }
    
    $this->setVar('list', $list, true);
  }
}
