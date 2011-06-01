<?php

/**
 * productCatalog components.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCatalogComponents extends myComponents
{
/**
  * Executes filter_price component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_price()
  {
    
  }
 /**
  * Executes filter_creator component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_creator()
  {
    $creatorList = CreatorTable::getInstance()->getListByProductCategory($this->productCategory);
    
    $list = array();
    foreach ($creatorList as $creator)
    {
      $list[] = array(
        'name'     => (string)$creator,
        'url'      => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $creator)),
      );
    }
    
    $this->setVar('list', $list, true);
  }
  /**
  * Executes filter_parameter component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_parameter()
  {
    $list = array();
    foreach ($this->productCategory->FilterGroup->Filter as $productFilter)
    {
      $options = array();
      foreach ($productFilter->Property->Option as $productPropertyOption)
      {
        $options[$productPropertyOption->id] = $productPropertyOption->value;
      }
      
      $list[] = array(
        'name'        => $productFilter->name,
        'type'        => $productFilter->type,
        'is_multiple' => $productFilter->is_multiple,
        'options'     => $options,
      );
    }
    
    $this->setVar('list', $list, true);
  }
}
