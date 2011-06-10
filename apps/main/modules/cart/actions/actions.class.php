<?php

/**
 * cart actions.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cartActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $cart = $this->getUser()->getCart();
    $this->setVar('cart', $cart, true);
  }
 /**
  * Executes add action
  *
  * @param sfRequest $request A request object
  */
  public function executeAdd(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->addProduct($product, $request['amount']);
    }

    $this->redirect($this->getRequest()->getReferer());
  }
 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->deleteProduct($product->id);
    }

    $this->redirect($this->getRequest()->getReferer());
  }
 /**
  * Executes clear action
  *
  * @param sfRequest $request A request object
  */
  public function executeClear(sfWebRequest $request)
  {
    $this->getUser()->getCart()->clear();

    $this->redirect($this->getRequest()->getReferer());
  }
}
