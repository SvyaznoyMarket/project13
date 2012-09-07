<?php
namespace light;
use Logger;

require dirname(__FILE__) . '/../light/lib/Config.php';

$httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';

if(in_array($httpHost, array(
    'enter.ru',
    'test.enter.ru',
    'nocache.enter.ru',
    'www.enter.ru',
    'demo.enter.ru'
)))
{
    $parameterList = require_once('../light/config/prod.php');
    $configLogMess = 'production config in use';
}
else
{
    $parameterList = require_once('../light/config/dev.php');
    $configLogMess = 'dev config in use';
}

Config::init($parameterList);

require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('rootPath').'lib/log4php/Logger.php');
require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'system/Controller.php');

TimeDebug::start('Total');
TimeDebug::start('Configure');




App::init();
Logger::getLogger('Settings')->debug($configLogMess);

Logger::getLogger('Settings')->info('core v2 url: '.Config::get('coreV2UserAPIUrl'));
Logger::getLogger('Settings')->info('core v1 url: '.Config::get('coreV1APIUrl'));
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
