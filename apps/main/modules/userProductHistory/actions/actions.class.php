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
  
  
  /**
   *
   * Получает краткую информацию о пользователе:
   * -имя
   * -о корзине
   * -о виш листе
   * -о сравниваемых товарах
   * -bingo TODO
   * Вызывается посредствам ajax. Параметров не принимает.
   * 
   * @param sfWebRequest $request
   * @return json 
   */
  public function executeShortinfo(sfWebRequest $request)
  {
      $user = $this->getUser();

      //подсчитываем общее количество и общую стоимость корзины
      $cart = $user->getCart();
      $qty = 0;
      $sum = 0;
      foreach($cart->getProducts()->toArray() as $id => $product){
          $qty += $product['cart']['quantity'];
          $sum += $product['price'] * $product['cart']['quantity'];
      }
      
      //отложенные товары
      $delayProducts = array();
      if ($user->getGuardUser())
      {
        $userDelayedProduct = new UserDelayedProduct();
        $delayProducts = $userDelayedProduct->getUserDelayProducts($user->getGuardUser()->id);
      }
      
#     echo '<pre>';
#     echo '</pre>';      
   #   exit();
      
      //имя есть только у авторизованного пользователя
      if ($user->isAuthenticated()) $name = $user->getName();
      else $name = false;
      return $this->renderJson(array(
        'success' => true,
        'data'    => array(
              'name' => $name,  
              'vitems' => $qty,
              'sum' => $sum,
              'vwish' => count($delayProducts),
              'vcomp' => $user->getProductCompare()->getProductsNum(),
              'bingo' => array()  
        ),
      ));  
      
      $this->redirect($this->getRequest()->getReferer());
      
  }
  
}
