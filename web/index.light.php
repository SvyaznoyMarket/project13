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

if(HTTP_HOST == 'enter.ru' || HTTP_HOST == 'test.enter.ru' || HTTP_HOST == 'nocache.enter.ru' || HTTP_HOST == 'www.enter.ru' || HTTP_HOST == 'demo.enter.ru'){
  require_once('../light/config/prod.php');
  $configLogMess = 'production config in use';
}
else if ('enter.loc' == HTTP_HOST) {
  require_once('../light/config/loc.php');
  $configLogMess = 'loc config in use';
}
else{
  require_once('../light/config/dev.php');
  $configLogMess = 'dev config in use';
}

App::init();
Logger::getLogger('Settings')->debug($configLogMess);

Logger::getLogger('Settings')->info('core v2 url: '.CORE_V2_USERAPI_URL);
Logger::getLogger('Settings')->info('core v1 url: '.CORE_V1_API_URL);
TimeDebug::end('Configure');

try{
  $routeString = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
  $routeString = str_replace('/index.light.php', '', $routeString);

  $route = App::getRouter()->matchUrl($routeString);
  $response = Controller::Run($route);
}
catch(Exception $e){
  Logger::getRootLogger()->warn('Exception: '.$e->getMessage());
  $response = Controller::Run('error.jsonErrorMessage', array('message' => $e->getMessage()));
}
TimeDebug::end('Total');
Logger::getLogger('Timer')->debug(TimeDebug::getAll());
$response->sendHeaders();
$response->sendContent();
