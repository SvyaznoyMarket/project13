<?php

/**
 * userDelayedProduct actions.
 *
 * @package    enter
 * @subpackage userDelayedProduct
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userDelayedProductActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->userDelayedProductList = $this->getUser()->getGuardUser()->getDelayedProduct();
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $product = $this->getRoute()->getObject();

    $userDelayedProduct = new UserDelayedProduct();
    $userDelayedProduct->fromArray(array(
      'user_id'    => $this->getUser()->getGuardUser()->id,
      'product_id' => $product->id,
    ));
    $userDelayedProduct->replace();

    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'result' => true,
        'data'   => array(
          'content' => $this->getComponent($this->getModuleName(), 'add_button', array('product' => $product))
        ),
      ));
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
    $product = $this->getRoute()->getObject();

    UserDelayedProductTable::getInstance()->createQuery()
      ->delete()
      ->where('user_id = ? AND product_id = ?', array($this->getUser()->getGuardUser()->id, $product->id))
      ->execute()
    ;

    $this->redirect($this->getRequest()->getReferer());
  }
 /**
  * Executes clear action
  *
  * @param sfRequest $request A request object
  */
  public function executeClear(sfWebRequest $request)
  {
    UserDelayedProductTable::getInstance()->createQuery()
      ->delete()
      ->where('user_id = ?', array($this->getUser()->getGuardUser()->id))
      ->execute()
    ;

    $this->redirect($this->getRequest()->getReferer());
  }
}
