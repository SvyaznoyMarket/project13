<?php
namespace light;

if(!class_exists('symfonyConfig')){
  require_once(ROOT_PATH.'lib/symfonyConfig.php');
}
$appConfig = symfonyConfig::parseConfig('app.yml', 'dev');
$coreConfig = $appConfig->get('app_core_config');
$onlineCall = $appConfig->get('app_online_call');

define('CORE_V2_USERAPI_URL', $coreConfig['userapi_url']);
define('CORE_V2_USERAPI_CLIENT_CODE', $coreConfig['client_code']);

define('CORE_V1_API_URL', $coreConfig['api_url']);
define('CORE_V1_CONSUMER_KEY', $coreConfig['consumer_key']);
define('CORE_V1_SIGNATURE', $coreConfig['signature']);

define('ONLINE_CALL_ENABLED', $onlineCall['enabled']);

define('DB_HOST', 'localhost');
define('DB_NAME', 'enter');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'qazwsxedc');
