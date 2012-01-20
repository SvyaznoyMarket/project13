<?php

/**
 * productCategory actions.
 *
 * @package    enter
 * @subpackage productCategory
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCategoryActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executeCarousel(sfWebRequest $request)
  {
    $this->setLayout(false);

    $productCategory = $this->getRoute()->getObject();
    $page = (int)$request->getParameter('page', 1);
    $limit = (int)$request->getParameter('limit', 3);
    if ($limit < 3)
    {
      $limit = 3;
    }
    if ($limit > 27)
    {
      $limit = 27;
    }
    $products = ProductTable::getInstance()->getListByCategory($productCategory, array(
      'offset'          => ($page-1) * 3,
      'limit'           => $limit,
      'view'            => 'list',
      'with_properties' => false,
      'property_view'   => false,
    ));

    $response = '';
    foreach ($products as $product)
    {
      $response .= $this->getComponent('product', 'show', array('view' => $productCategory->has_line ? 'line' : 'compact', 'product' => $product));
    }
    $this->renderText($response);

    return sfView::NONE;
  }

  public function executeMenu(sfWebRequest $request)
  {

  }
}
