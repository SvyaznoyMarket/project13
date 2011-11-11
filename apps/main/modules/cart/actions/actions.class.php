<?php

/**
 * cart actions.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cartActions extends myActions
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
    $result['value'] = true;
    $result['error'] = "";
    //валидация количества товара
    if ( !isset($request['quantity'])) $request['quantity'] = 1;
    elseif ( (string)(int)$request['quantity']!==(string)$request['quantity'])
    {
        $result['value'] = false;
        $result['error'] = "Некорректное количество товара.";
    }

    if ($result['value']){
        $product = ProductTable::getInstance()->findOneByToken($request['product']);

        if ($product)
        {
            try{

                $currentNum = $this->getUser()->getCart()->getQuantityByToken($request['product']);
                $request['quantity'] += $currentNum;

                if ($request['quantity']<=0){
                    $request['quantity'] = 0;
                    $this->getUser()->getCart()->deleteProduct($product['id']);
                }
                else{
                    $this->getUser()->getCart()->addProduct($product, $request['quantity']);
                }
            }
            catch(Exception $e){
                $result['value'] = false;
                $result['error'] = "Не удалось добавить в корзину товар token='".$request['product']."'.";
            }
        }
        else
        {
            $result['value'] = false;
            $result['error'] = "Товар token='".$request['product']."' не найден.";
        }
    }
    
    //если помимо товаров надо добавить в карзину сервисы
    $services = $request['services'];
    if ($services){  
        $servicesAr = json_decode($services);
        #var_dump($servicesAr);
        #echo get_class( $this->getUser()->getCart() );
        foreach($servicesAr as $servToken => $qty){
            $service = ServiceTable::getInstance()->findOneByToken($servToken);
            if (!$service) continue;
            $this->getUser()->getCart()->addService($product, $service, $qty);
        }
        
    }

    if ($request->isXmlHttpRequest())
    {
          if ($result['value'])
          {
              $return = array(
                'success' => $result['value'],
                'data'   => array(
                    'quantity' => $request['quantity'],
                    'html' => $this->getComponent($this->getModuleName(),'buy_button',array('product' => $product))
                )
              );
          }
          else
          {
              $return = array(
                'success' => $result['value'],
                'data'   => array(
                    'error' => $result['error']
                )
              );
          }
          return $this->renderJson($return);
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

    if ($request->isXmlHttpRequest())
    {
      return $this->renderJson(array(
        'success' => true,
      ));
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

  public function executeServiceAdd(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);
    $service = ServiceTable::getInstance()->findOneByToken($request['service']);

    $this->getUser()->getCart()->addService($product, $service, $request['quantity']);

    $this->redirect($this->getRequest()->getReferer());
  }

  public function executeServiceDelete(sfWebRequest $request)
  {
    $product = ProductTable::getInstance()->findOneByToken($request['product']);
    $service = ServiceTable::getInstance()->findOneByToken($request['service']);

    $this->getUser()->getCart()->deleteService($product, $service);

    $this->redirect($this->getRequest()->getReferer());
  }

}
