<?php
namespace light;

$baseConfig = require 'main.php';

if(!class_exists('symfonyConfig')){
  require_once($baseConfig['rootPath'].'lib/symfonyConfig.php');
}

$appConfig = symfonyConfig::parseConfig('app.yml', 'live');
$coreConfig = $appConfig->get('app_core_config');
$onlineCall = $appConfig->get('app_online_call');

$productionConfig = array(
  'coreV2UserAPIUrl' => $coreConfig['userapi_url'],
  'coreV2UserAPIClientCode' => $coreConfig['client_code'],
  'coreV1APIUrl' => $coreConfig['api_url'],
  'coreV1ConsumerKey' => $coreConfig['consumer_key'],
  'coreV1Signature' => $coreConfig['signature'],
  'onlineCallEnabled' => $onlineCall['enabled'],
  'isProduction' => True,
  'wpUrl' => 'http://content.enter.ru/',
  'db' => array(
    'host' => '10.20.33.2',
    'name' => 'enter',
    'user' => 'root',
    'password' => 'qazwsxedc'
  ),
  'smartEngine' => array(
    'apiUrl' => 'https://selightprod.smartengine.at/se-light/api/1.0/json/',
    'apiKey' => 'c41851b19511c20acc84f47b7816fb8e',
    'tenantid' => 'ENojUTRcD8',
    'cert' => $baseConfig['rootPath'].'../data/cert/selightprod.crt',
  )
);

define('SMARTENGINE_CERT', $baseConfig['rootPath'].'../data/cert/selightprod.crt');

return array_merge($baseConfig, $productionConfig);