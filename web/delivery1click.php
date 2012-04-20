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
TimeDebug::start('Total');
TimeDebug::start('Configure');

if(HTTP_HOST == 'enter.ru'){
  require_once('../light/config/prod.php');
}
else{
  require_once('../light/config/dev.php');
}
TimeDebug::end('Configure');

require_once(ROOT_PATH.'system/Request.php');
require_once(ROOT_PATH.'system/Response.php');
require_once(ROOT_PATH.'system/Controller.php');
require_once(ROOT_PATH.'system/Router.php');

//$_POST = array('product_id' => 484, 'product_quantity' => 1, 'region_id' => 14974);

if(isset($_POST['product_id']) && isset($_POST['product_quantity'])){
  if(isset($_POST['region_id'])){
    $region_id = intval($_POST['region_id']);
  }
  else{
    $region_id = 14974; //TODO реализовать класс CurrentUser
  }
  $route = new Route(array('class' => 'delivery', 'method' => 'ProductDeliveryJson'), array('product_id' => intval($_POST['product_id']), 'quantity' => intval($_POST['product_quantity']), 'region' => $region_id));
}
else{
  $route = new Route(array('class' => 'error', 'method' => 'jsonErrorMessage'), array('message' => 'bad request params'));
}


$response = Controller::Run($route);
TimeDebug::end('Total');
$response->sendHeaders();
$response->sendContent();
