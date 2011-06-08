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
  public function executeAdd(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->addProduct($product, $request['amount']);
    }

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeDelete(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->deleteProduct($product->id);
    }

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeShow()
  {
    $cart = $this->getUser()->getCart();
    $this->setVar('cart', $cart, true);
  }

  public function executeClear()
  {
    $this->getUser()->getCart()->clear();
    $this->redirect($this->getRequest()->getReferer());
  }
}
