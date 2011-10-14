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
  * @param view $view Вид
  */
  public function executeList()
  {
    if (!in_array($this->view, array('default', 'carousel', 'preview')))
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
  * Executes root_list component
  *
  * @param ProductCategory $productCategory Текущая категория товара
  */
  public function executeRoot_list()
  {
//    $list = array();
//    foreach (ProductCategoryTable::getInstance()->getRootList() as $productCategory)
//    {
//      $list[] = array(
//        'name' => (string)$productCategory,
//        'url'  => url_for('productCatalog_category', $productCategory),
//      );
//    }

    $this->setVar('list', ProductCategoryTable::getInstance()->getRootList(), true);
  }
  
  public function executeExtra_menu()
  {
	  $data = ProductCategoryTable::getInstance()->getSubList();
	  $result = array();
	  foreach ($data as $row) {
		  if (!isset($result[$row->root_id])) {
			  $result[$row->root_id] = array();
		  }
		  $result[$row->root_id][] = $row;
	  }
	  $this->setVar('rootlist', $result, true);
  }
 /**
  * Executes child_list component
  *
  * @param ProductCategory $productCategory Родительская категория товара
  * @param view $view Вид
  */
  public function executeChild_list()
  {
    if (!$this->view)
    {
      $this->view = 'default';
    }

    $this->setVar('productCategoryList', $this->productCategory->getNode()->getChildren());
  }
 /**
  * Executes show component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param view $view Вид
  */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'carousel', 'preview')))
    {
      $this->view = 'default';
    }

    $item = array(
      'name'             => (string)$this->productCategory,
      'url'              => url_for('productCatalog_category', $this->productCategory),
	  'carousel_data_url'=> url_for('productCatalog_carousel', $this->productCategory),
      'product_quantity' => $this->productCategory->countProduct(),
	  'links'            => $this->productCategory->getLink(),
    );
    
    if ('carousel' == $this->view)
    {
      if (0 == $item['product_quantity'])
      {
        return sfView::NONE;
      }
      
      $item['product_list'] = ProductTable::getInstance()->getListByCategory($this->productCategory, array(
        'limit' => 6,
      ));
    }    
    if ('preview' == $this->view)
    {
      if (0 == $item['product_quantity'])
      {
        return sfView::NONE;
      }
      
      $item['product'] = $this->productCategory->getPreviewProduct();
    }

    $this->setVar('item', $item, true);
  }
}
