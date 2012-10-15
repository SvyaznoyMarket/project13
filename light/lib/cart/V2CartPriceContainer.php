<?php
namespace light;

require_once('interface/CartPriceContainer.php');
require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 19.06.12
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */
class V2CartPriceContainer implements CartPriceContainer
{

  public function getPrices(CartContainer $cart){
    try{
      if(is_null($cart) || (!count($cart->getProductsQuantities()) && !count($cart->getServicesQuantities()))){
        return array('product_list' => array(), 'service_list' => array(), 'warranty_list' => array(), 'price_total' => 0);
      }

      $response = App::getCoreV2()->query(
        'cart.get-price',
        array('geo_id' => App::getCurrentUser()->getRegion()->getId()),
        array(
          'product_list' => $cart->getProductsQuantities(),
          'service_list' => $cart->getServicesQuantities(),
          'warranty_list' => $cart->getWarrantiesQuantities(),
      ));

      return (array) $response;
    }
    catch(\Exception $e){
      return array('product_list' => array(), 'service_list' => array(), 'warranty_list' => array(), 'price_total' => 0);
    }
  }
}

class MockCartPriceContainer implements CartPriceContainer
{

  public function getPrices(CartContainer $cart){

    $servicesQuantities = $cart->getServicesQuantities();
    $serviceList = array();

    foreach($servicesQuantities as $service){
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

    $warrantyQuantities = $cart->getWarrantiesQuantities();
    $warrantyList = array();

    foreach($warrantyQuantities as $warranty){
      $tmp = $warranty;
      $tmp['price'] = 900;
      $warrantyList[] = $tmp;
    }

    return array(
      'product_list'  => $productList,
      'service_list'  => $serviceList,
      'warranty_list' => $warrantyList,
      'price_total'   => 9000
    );
  }
}
