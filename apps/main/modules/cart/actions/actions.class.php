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

    //$productTable = ProductTable::getInstance();
    //$product = Doctrine_Manager::connection()->queryOne("SELECT * FROM product WHERE id = ?", array(10, ));
    //myDebug::dump($product);

    $cart = $this->getUser()->getCart();
    //$product = ProductTable::getInstance()->getById(1);
    //myDebug::dump($product);
    //$cart->addProduct($product);
    //myDebug::dump($cart->dump());
    //$cart->addProduct($product, 10);
    //$product = ProductTable::getInstance()->getById(5);
    //$cart->addProduct($product, 2);
    //$product = $cart->getProduct(1);
    //myDebug::dump($cart->hasProduct($product));
    //$cart->clear();
    //$cart->deleteProduct(1);
    $products = $cart->getProducts()->toArray();
    myDebug::dump($products);
    //$cart->getProducts()->getData();
    //echo "<pre>"; print_r($cart->getProducts()->toArray()); echo "</pre>";
    //myDebug::dump($cart->getProducts()->toArray());
    //$cart->deleteProduct(1);
    //myDebug::dump($cart->dump());

  }

  public function executeAdd(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->addProduct($product, $request['amount']);
    }

    $this->redirect($this->getRequest()->getReferer(), 302);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);

    if ($product)
    {
      $this->getUser()->getCart()->deleteProduct($product->id);
    }

    $this->redirect($this->getRequest()->getReferer(), 302);
  }

  public function executeShow()
  {
    $cart = $this->getUser()->getCart();
    $this->setVar('cart', $cart, true);
  }

  public function executeClear()
  {
    $this->getUser()->getCart()->clear();
    $this->redirect($this->getRequest()->getReferer(), 302);
  }
}
