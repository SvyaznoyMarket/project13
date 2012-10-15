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
        array(
          'product_list'  => $cart->getProductsQuantities(),
          'service_list'  => $cart->getServicesQuantities(),
          'warranty_list' => $cart->getWarrantiesQuantities(),
      ));

      return (array)$response;
    }
    catch(Exception $e){
      return array('product_list' => array(), 'service_list' => array(), 'warranty_list' => array(), 'price_total' => 0);
    }
  }
}