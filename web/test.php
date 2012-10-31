<?php
namespace light;
use Logger;

//session_start();
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 24.04.12
 * Time: 10:46
 * To change this template use File | Settings | File Templates.
 */

require_once('../light/config/main.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(ROOT_PATH.'lib/log4php/Logger.php');
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'system/Controller.php');

TimeDebug::start('Total');
TimeDebug::start('Configure');

//if(HTTP_HOST == 'enter.ru' || HTTP_HOST == 'test.enter.ru' || HTTP_HOST == 'nocache.enter.ru' || HTTP_HOST == 'www.enter.ru' || HTTP_HOST == 'demo.enter.ru'){
//  require_once('../light/config/prod.php');
//  $configLogMess = 'production config in use';
//}
//else{
  require_once('../light/config/dev.php');
  $configLogMess = 'dev config in use';
//}
TimeDebug::end('Configure');
try{
  App::init();
  Logger::getLogger('Settings')->debug($configLogMess);
  Logger::getLogger('Settings')->info('core v2 url: '.CORE_V2_USERAPI_URL);
  Logger::getLogger('Settings')->info('core v1 url: '.CORE_V1_API_URL);

  $data = array(
    'delivered_at' => '2012-08-04',
    'recipient_first_name'  => 'test',
    'recipient_phonenumber' => '+7 0000000000',
    'shop_id' => 24,
    'product' => array(array('id' => 34752, 'quantity' => 1)),
//    'product' => array(array('id' => 23425, 'quantity' => 1)),
  );

//  $deliveryList = App::getDelivery()->getProductDeliveries(34752, 1, App::getCurrentUser()->getRegion()->getId());

//  $productList = App::getProduct()->getProductsByIdList(array(23425));
  $productList = App::getProduct()->getProductsByIdList(array(34752));
  $product = $productList[0];

  if($product->isKit()){
    $productList = array();

  }

  if($product->isKit()){
    $kitList = $product->getKitList();
    foreach($kitList as $kitElem){
      $productList[] = array('id' => $kitElem->getProductId(), 'quantity' => ($kitElem->getQuantity() * $data['product'][0]['quantity']));
    }
  }
  else{
    $productList = array(array('id' => $data['product'][0]['id'], 'quantity' => $data['product'][0]['quantity']));
  }

  $data['product'] = $productList;

  $delivery = App::getDelivery()->getProductDeliveries($data['product'][0]['id'], $data['product'][0]['quantity'], App::getCurrentUser()->getRegion()->getId());

  var_export($delivery);


//  var_export($product);

  $order = App::getOrder()->getOrderFromOneClickArray($data);

//  $productIdList = array_map(function($i) {return $i['id'];}, $productList);
//
//  $productInfo = App::getProduct()->getProductsByIdList($productIdList);
//
//  var_export($productInfo);


}
catch(Exception $e){
  Logger::getRootLogger()->warn('Exception: '.$e->getMessage());
}
TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());