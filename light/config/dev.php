<?php
namespace light;

$baseConfig = require 'main.php';

if(!class_exists('symfonyConfig')){
    require_once($baseConfig['rootPath'].'lib/symfonyConfig.php');
}

$appConfig = symfonyConfig::parseConfig('app.yml', 'dev');
$coreConfig = $appConfig->get('app_core_config');
$onlineCall = $appConfig->get('app_online_call');

$developmentConfig = array(
    'coreV2UserAPIUrl' => $coreConfig['userapi_url'],
    'coreV2UserAPIClientCode' => $coreConfig['client_code'],
    'coreV1APIUrl' => $coreConfig['api_url'],
    'coreV1ConsumerKey' => $coreConfig['consumer_key'],
    'coreV1Signature' => $coreConfig['signature'],
    'onlineCallEnabled' => $onlineCall['enabled'],
    'isProduction' => False,
    'wpUrl' => 'http://content.enter.n/',
    'db' => array(
        'host' => 'localhost',
        'name' => 'enter',
        'user' => 'root',
        'password' => 'qazwsxedc'
    ),
    'debug' => True
);

return array_merge($baseConfig, $developmentConfig);