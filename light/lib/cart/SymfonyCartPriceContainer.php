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

    $ServicesQuantities = $cart->getServicesQuantities();
    $serviceList = array();

    foreach($ServicesQuantities as $service){
      $tmp = $service;
      $tmp['price'] = 1500;
      $serviceList[] = $tmp;
    }

    $productsQuantities = $cart->getProductsQuantities();
    $productList = array();

    foreach($productsQuantities as $product){
      $tmp = $product;
      $tmp['price'] = 500;
      $productList[] = $tmp;
    }

    return array(
      'product_list' => $productList,
      'service_list' => $serviceList,
      'price_total' => 9000
    );
  }
}