<?php

/**
 * userProductHistory actions.
 *
 * @package    enter
 * @subpackage userProductHistory
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductHistoryActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->setVar('productList', $this->getUser()->getProductHistory()->getProducts(), true);
  }
 /**
  * Executes clear action
  *
  * @param sfRequest $request A request object
  */
  public function executeClear(sfWebRequest $request)
  {
    $this->getUser()->getProductHistory()->clear();

    $this->redirect($this->getRequest()->getReferer());
  }
  
  
  public function executeShortinfo(sfWebRequest $request)
  {
      //echo '<pre>';
      $user = $this->getUser();
      //подсчитываем общее количество и общую стоимость корзины
      $cart = $user->getCart();
      $qty = 0;
      $sum = 0;
      foreach($cart->getProducts()->toArray() as $id => $product){
          $qty += $product['cart']['quantity'];
          $sum += $product['price'] * $product['cart']['quantity'];
      }
                  
      $userDelayedProduct = new UserDelayedProduct();
      $delayProducts = $userDelayedProduct->getUserDelayProducts($user->id);
      
      return $this->renderJson(array(
        'success' => true,
        'data'    => array(
              'name' => $user->getAttribute('name'),  
              'vitems' => $qty,
              'sum' => $sum,
              'vwish' => count($delayProducts),
              'vcomp' => 1,
              'bingo' => array()  
        ),
      ));      
      //echo '</pre>';
     // exit();
  }
}
