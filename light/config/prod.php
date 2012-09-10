<?php
namespace light;

if(!class_exists('symfonyConfig')){
  require_once(ROOT_PATH.'lib/symfonyConfig.php');
}
$appConfig = symfonyConfig::parseConfig('app.yml', 'live');
$coreConfig = $appConfig->get('app_core_config');
$onlineCall = $appConfig->get('app_online_call');

define('CORE_V2_USERAPI_URL', $coreConfig['userapi_url']);
define('CORE_V2_USERAPI_CLIENT_CODE', $coreConfig['client_code']);

define('CORE_V1_API_URL', $coreConfig['api_url']);
define('CORE_V1_CONSUMER_KEY', $coreConfig['consumer_key']);
define('CORE_V1_SIGNATURE', $coreConfig['signature']);

define('ONLINE_CALL_ENABLED', $onlineCall['enabled']);

define('DB_HOST', '10.20.33.2');
define('DB_NAME', 'enter');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'qazwsxedc');

define('SMARTENGINE_API_URL', 'https://selightstage.smartengine.at/se-light/api/1.0/json/');
define('SMARTENGINE_API_KEY', 'c41851b19511c20acc84f47b7816fb8e');
define('SMARTENGINE_TENANTID', 'ENojUTRcD8');
define('SMARTENGINE_CERT_PATH', ROOT_PATH.'data/cert/smartengine-server2.pem');