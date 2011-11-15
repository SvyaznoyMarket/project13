<?php

/**
 * line components.
 *
 * @package    enter
 * @subpackage line
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class lineComponents extends myComponents
{
/**
  * Executes navigation component
  *
  * @param ProductCategory $productCategory Категория товара
  * @param Creator $creator Производитель
  */
  public function executeNavigation()
  {
    $list = array();
    $mainProduct = ProductTable::getInstance()->getByLine($this->line);
    $productCategory = $mainProduct->getMainCategory();
    $rootCategory = $productCategory->getRootCategory();

    if ($rootCategory)
    {
      $list[] = array(
        'name' => (string)$rootCategory,
        'url'  => url_for('productCatalog_category', $rootCategory),
      );
    }

    if ($productCategory)
    {
      $list[] = array(
        'name' => (string)$productCategory,
        'url'  => url_for('productCatalog_category', $productCategory),
      );
    }

    if (isset($this->creator))
    {
      $list[] = array(
        'name' => (string)$this->creator,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->productCategory, 'creator' => $this->creator)),
      );
    }
    
    $list[] = array(
      'name' => 'Серия '.(string)$this->line,
      'url'  => url_for('lineCard', $this->line),
    );

    $this->setVar('list', $list, false);
  }
 /**
  * Executes mainProduct component
  *
  */
  public function executeMain_product()
  {
    if (empty($this->line))
    {
      return sfView::NONE;
    }
    
    $product = ProductTable::getInstance()->getByLine($this->line);
    
    $item = array(
      'article'     => $product->article,
      'name'        => (string) $product,
      'creator'     => (string) $product->Creator,
      'price'       => $product->formatted_price,
      'has_link'    => $product['view_show'],
      'photo'       => $product->getMainPhotoUrl(3),
      'product'     => $product,
      'url'         => url_for('productCard', $product, array('absolute' => true)),
      'stock_url'   => url_for('productStock', $product),
      'shop_url'    => url_for('shop_show', ShopTable::getInstance()->getMainShop()),
      'description' => $product->description,
    );
    
    $this->setVar('item', $item, true);
  }
  
  /**
   * Executes pager component
   *
   * @param myDoctrinePager $pager Листалка товаров
   */
  public function executePager()
  {
    $this->view = isset($this->view) ? $this->view : $this->getRequestParameter('view');
    if (!in_array($this->view, array('expanded', 'compact', )))
    {
      $this->view = 'compact';
    }

    $this->setVar('list', $this->pager->getResults(null, array(
      'with_properties' => 'expanded' == $this->view ? true : false,
      'view'            => 'list',
    )), true);
  }
}
