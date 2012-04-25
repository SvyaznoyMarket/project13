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
        'url'  => $this->generateUrl('productCatalog_category', $rootCategory),
      );
    }

    if ($productCategory)
    {
      $list[] = array(
        'name' => (string)$productCategory,
        'url'  => $this->generateUrl('productCatalog_category', $productCategory),
      );
    }

    $list[] = array(
      'name' => 'Серия '.(string)$this->line,
      'url'  => $this->generateUrl('lineCard', $this->line),
    );

    $this->setVar('list', $list, false);
  }
 /**
  * Executes mainProduct component
  *
  */
  public function executeMain_product()
  {
    if (empty($this->line) || empty($this->product_id))
    {
      return sfView::NONE;
    }

    $this->product = ProductTable::getInstance()->getById($this->product_id);

    $item = array(
      'article'     => $this->product->article,
      'name'        => (string) $this->product,
      'creator'     => (string) $this->product->Creator,
      'price'       => $this->product->formatted_price,
      'has_link'    => $this->product['view_show'],
      'photo'       => $this->product->getMainPhotoUrl(3),
      'product'     => $this->product,
      'url'         => $this->generateUrl('productCard', $this->product, array('absolute' => true)),
      'stock_url'   => $this->generateUrl('productStock', $this->product),
      'shop_url'    => $this->generateUrl('shop_show', ShopTable::getInstance()->getMainShop()),
      'description' => $this->product->description,
      'part'        => array(),
    );

    if ($this->product->isKit())
    {
      $parts = $this->product->getPart();
      foreach ($parts as $part)
      {
        $item['part'][] = array(
          'name'  => $part->name,
          'photo' => $part->getMainPhotoUrl(1),
          'url'   => $this->generateUrl('productCard', $part),
        );
      }
    }

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
