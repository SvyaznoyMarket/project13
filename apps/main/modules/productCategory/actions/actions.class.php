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
	  sfConfig::set('sf_web_debug', false);
	  $page = (int)$request->getParameter('page', 1);
	  $limit = (int)$request->getParameter('limit', 3);
	  if ($limit < 3) {
		  $limit = 3;
	  }
	  if ($limit > 27) {
		  $limit = 27;
	  }
	  $products = ProductTable::getInstance()->getListByCategory($this->getRoute()->getObject(), array(
		'offset' => ($page-1)*3,
        'limit'  => $limit,
        'view' => 'list',
      ));
	  $this->setLayout(false);
//	  $this->getContext()->getConfiguration()->loadHelpers('Url');
    $response = '';
	  foreach ($products as $product) {
		  $response .= $this->getComponent('product', 'show', array('view' => 'compact', 'product' => $product));
	  }
    $this->renderText($response);
	  return sfView::NONE;
  }
}
