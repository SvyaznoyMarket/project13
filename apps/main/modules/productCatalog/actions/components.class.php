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
  * Executes filter_product_parameter component
  *
  * @param ProductCategory $productCategory Категория товара
  */
  public function executeFilter_product_parameter()
  {
    
  }
}
