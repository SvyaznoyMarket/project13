<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 22.06.12
 * Time: 13:39
 * To change this template use File | Settings | File Templates.
 */
class SymfonyCartPriceContainer implements \light\CartPriceContainer
{

  public function getPrices(\light\CartContainer $cart){
    try{
      if(is_null($cart) || (!count($cart->getProductsQuantities()) && !count($cart->getServicesQuantities()))){
        return array('product_list' => array(), 'service_list' => array(), 'warranty_list' => array(), 'price_total' => 0);
      }
      $region = RepositoryManager::getRegion()->getDefaultRegionId();
      $response = CoreClient::getInstance()->query(
        'cart.get-price',
        array('geo_id' =>$region),
        array('product_list' => $cart->getProductsQuantities(), 'service_list' => $cart->getServicesQuantities())
      );

      // MOCK
      $response['warranty_list'] = array(
        array(
          'warranty_id' => 1,
          'product_id'  => 4696,
          'quantity'    => 1,
          'price'       => 900,
        ),
      );

      return (array)$response;
    }
    catch(Exception $e){
      return array('product_list' => array(), 'service_list' => array(), 'warranty_list' => array(), 'price_total' => 0);
    }
  }
}