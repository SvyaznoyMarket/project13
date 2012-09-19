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
    'wpUrl' => 'http://content.enter.ru/',
    'db' => array(
        'host' => 'localhost',
        'name' => 'enter',
        'user' => 'root',
        'password' => 'qazwsxedc'
    ),
    'debug' => True,
    'smartEngine' => array(
        'apiUrl' => 'https://selightstage.smartengine.at/se-light/api/1.0/json/',
        'apiKey' => 'c41851b19511c20acc84f47b7816fb8e',
        'tenantid' => 'ENojUTRcD8',
        'cert' => null,
    )
);
define('SMARTENGINE_CERT', null);

return array_merge($baseConfig, $developmentConfig);