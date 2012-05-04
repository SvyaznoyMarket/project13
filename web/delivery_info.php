<?php
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
Logger::configure(LOGGER_CONFIG_PATH); //В отдельную константу вынесено - что бы можно было иметь разные конфиги для dev и prod
TimeDebug::start('Total');
TimeDebug::start('Configure');

if(HTTP_HOST == 'enter.ru' || HTTP_HOST == 'nocache.enter.ru'){
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

//$_POST = array('ids' => array(22859, 1208, 11914, 425, 5440, 22727, 23296, 12385, 2691, 3741, 7552, 10825, 11587, 13113, 15137, 23076, 848, 990), 'region_id' => 14974);
//$_POST = array('ids' => array(4435), 'region_id' => 14974);

if(isset($_POST['ids'])){
  if(isset($_POST['region_id'])){
    $region_id = intval($_POST['region_id']);
  }
  else{
    $region_id = 14974; //TODO реализовать класс CurrentUser
  }
  $route = new Route(array('class' => 'delivery', 'method' => 'ProductListShortDeliveryJson'), array('products' => $_POST['ids'], 'region' => $region_id));
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
