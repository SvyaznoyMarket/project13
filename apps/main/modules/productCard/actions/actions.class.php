<?php

/**
 * productCard actions.
 *
 * @package    enter
 * @subpackage productCard
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCardActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
	
    $title = $this->product['name'];
    $mainCat = $this->product->getMainCategory();
    if ($mainCat) {
      $title .= ' – '.$mainCat;
      $rootCat = $mainCat->getRootRow();
      if ($rootCat->id !== $mainCat->id) {
        $title .= ' – '.$rootCat;
      }
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');

    // история просмотра товаров
    $this->getUser()->getProductHistory()->addProduct($this->product);
  }
 /**
  * Executes preview action
  *
  * @param sfRequest $request A request object
  */
  public function executePreview(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->product = ProductTable::getInstance()->find($request['product']);
    
    $this->redirect(array('sf_route' => 'productCard', 'sf_subject' => $this->product), 301);
  }
}
