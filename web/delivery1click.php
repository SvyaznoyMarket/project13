<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 19.04.12
 * Time: 16:37
 * To change this template use File | Settings | File Templates.
 */

require_once('../light/config/main.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(ROOT_PATH.'lib/log4php/Logger.php');
Logger::configure(LOGGER_CONFIG_PATH); //В отдельную константу вынесено - что бы можно было иметь разные конфиги для dev и prod
TimeDebug::start('Total');
TimeDebug::start('Configure');

if(HTTP_HOST == 'enter.ru' || HTTP_HOST == 'test.enter.ru' || HTTP_HOST == 'nocache.enter.ru' || HTTP_HOST == 'www.enter.ru' || HTTP_HOST == 'demo.enter.ru'){
  require_once('../light/config/prod.php');
  Logger::getLogger('Settings')->debug('production config in use');
}
else{
  require_once('../light/config/dev.php');
  Logger::getLogger('Settings')->debug('dev config in use');
}

Logger::getLogger('Settings')->info('core v2 url: '.CORE_V2_USERAPI_URL);
Logger::getLogger('Settings')->info('core v1 url: '.CORE_V1_API_URL);
TimeDebug::end('Configure');

require_once(ROOT_PATH.'system/Request.php');
require_once(ROOT_PATH.'system/Response.php');
require_once(ROOT_PATH.'system/Controller.php');
require_once(ROOT_PATH.'system/Router.php');

//$_POST = array('product_id' => 484, 'product_quantity' => 1, 'region_id' => 14974);

$geoIPCodeMapping = array(
  '43'    => 99,
  '47_4'  => 1964,
  '47_3'  => 1965,
  '47_1'  => 6125,
  '47'    => 8440,
  '47_5'  => 9748,
  '47_2'  => 10358,
  '62'    => 10374,
  '09'    => 13241,
  '69'    => 13242,
  '77'    => 18073,
  '86'    => 18074,
  '48'    => 14974,
  '76'    => 74358,
  '41'    => 74562,
);

if(isset($_POST['product_id']) && isset($_POST['product_quantity'])){
  if(isset($_POST['region_id'])){
    $region_id = intval($_POST['region_id']);
  }
  elseif(isset($_COOKIE['geoshop']) && array_key_exists($_COOKIE['geoshop'], $geoIPCodeMapping)){
    $region_id = $geoIPCodeMapping[$_COOKIE['geoshop']];
  }
  else{
    $region_id = 14974; //TODO реализовать класс CurrentUser
  }
  $route = new Route(array('class' => 'delivery', 'method' => 'ProductDeliveryJson'), array('product_id' => intval($_POST['product_id']), 'quantity' => intval($_POST['product_quantity']), 'region' => $region_id));
}
else{
  $route = new Route(array('class' => 'error', 'method' => 'jsonErrorMessage'), array('message' => 'bad request params'));
}

try{
  $response = Controller::Run($route);
}
catch(Exception $e){
  Logger::getRootLogger()->warn('Exception: '.$e->getMessage());
  $route = new Route(array('class' => 'error', 'method' => 'jsonErrorMessage'), array('message' => $e->getMessage()));
  $response = Controller::Run($route);
}
TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());
$response->sendHeaders();
$response->sendContent();
