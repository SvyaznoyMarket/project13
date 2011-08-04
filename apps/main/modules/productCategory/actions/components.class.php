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
    if (!in_array($this->view, array('default')))
    {
      $this->view = 'default';
    }

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
 /**
  * Executes list_root component
  *
  * @param ProductCategory $productCategory Текущая категория товара
  */
  public function executeList_root()
  {
    $list = array();
    foreach (ProductCategoryTable::getInstance()->getRootList() as $productCategory)
    {
      $list[] = array(
        'name'            => (string)$productCategory,
        'productCategory' => $productCategory,
      );
    }

    $this->setVar('list', $list, true);
  }
}
