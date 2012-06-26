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

require_once('../light/config/dev.php');
$configLogMess = 'dev config in use';


App::init();
Logger::getLogger('Settings')->debug($configLogMess);

Logger::getLogger('Settings')->info('core v2 url: '.CORE_V2_USERAPI_URL);
Logger::getLogger('Settings')->info('core v1 url: '.CORE_V1_API_URL);
TimeDebug::end('Configure');

try{
//  $routeString = '/cart/add/23425/_quantity/2';
  $routeString = '/cart/add_service/23425/_service/93/_quantity/3';
  $route = App::getRouter()->matchUrl($routeString);
  $response = Controller::Run($route);
}
catch(Exception $e){
  Logger::getRootLogger()->warn('Exception: '.$e->getMessage());
  $response = Controller::Run('error.jsonErrorMessage', array('message' => $e->getMessage()));
}
TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());
//$response->sendHeaders();
$response->sendContent();
