<?php

/**
 * productCatalog actions.
 *
 * @package    enter
 * @subpackage productCatalog
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCatalogActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->productCategoryList = ProductCategoryTable::getInstance()->getList();
  }
 /**
  * Executes category action
  *
  * @param sfRequest $request A request object
  */
  public function executeCategory(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    
    $this->productList = ProductTable::getInstance()->getListByCategory($this->productCategory, array(
      'order' => 'product.name',
      'limit' => sfConfig::get('app_productCatalog_product_limit', 20),
      'view'  => 'list',
    ));
  }
 /**
  * Executes creator action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreator(sfWebRequest $request)
  {
    $this->productCategory = $this->getRoute()->getObject();
    $this->creator = $this->getRoute()->getCreatorObject();
    
    $this->productList = ProductTable::getInstance()->getListbyCategory($this->productCategory, array(
      'creator' => $this->creator,
      'order'   => 'product.name',
      'limit'   => sfConfig::get('app_productCatalog_product_limit', 20),
      'view'    => 'list',
    ));
  }
}
