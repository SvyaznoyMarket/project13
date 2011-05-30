<?php

/**
 * productCategory components.
 *
 * @package    enter
 * @subpackage productCategory
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCategoryComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param Doctrine_Collection $productCategoryList Коллекция категорий товаров
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->productCategoryList as $productCategory)
    {
      $list[] = array(
        'name'            => (string)$productCategory,
        'productCategory' => $productCategory,
      );
    }
    
    $this->setVar('list', $list, true);
  }
}
