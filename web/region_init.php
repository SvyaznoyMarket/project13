<?php
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
  header('HTTP/1.0 404 Not Found');
  require('../apps/main/modules/default/templates/error404Success.php');
  exit();
}

//устанавливаю заголовок ответа json
header('Content-Type:	application/json');

define('MODE', 'prod');
define('SESSION_NAME', 'enter');
define('CURRENT_REGION_COOKIE_NAME', 'geoshop');
try
{
  require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/yaml/sfYamlParser.php';
  $yaml = new sfYamlParser();
  $dbConfig = $yaml->parse(file_get_contents(dirname(__FILE__) . '/../config/databases.yml'));
  if (!isset($dbConfig[MODE])) {
    if (!isset($dbConfig['all'])) {
      throw new Exception('cant load config');
    }
    $dbConfig = $dbConfig['all'];
  }
  else {
    $dbConfig = $dbConfig[MODE];
  }

  if (!isset($dbConfig['doctrine']['param'])) {
    throw new Exception('cant load config');
  }

  $dbConfig = array_merge(array('host' => '', 'username' => '', 'password' => '', 'dbname' => ''), $dbConfig['doctrine']['param']);

  $dsn = str_replace('mysql:', '', $dbConfig['dsn']);
  $params = explode(';', $dsn);
  foreach ($params as $param) {
    list($key, $val) = explode('=', $param);
    $key = trim($key);
    $dbConfig[$key] = trim($val);
  }
  //подключаюсь к базе
  if (!($conn = mysql_connect($dbConfig['host'], $dbConfig['username'], $dbConfig['password']))) {
    throw new Exception('cant connect to db "' . $dbConfig['host'] . '"');
  }

  //переключаюсь на нужную базу
  if (!mysql_select_db($dbConfig['dbname'], $conn)) {
    throw new Exception('cant select db "' . $dbConfig['dbname'] . '"');
  }
}
catch (Exception $e)
{
  if ($conn) {
    mysql_close($conn);
  }
  //	echo $e;
  die(json_encode(array('success' => false, 'data' => array())));
}

$needSendCookie = true;

if(isset($_COOKIE[CURRENT_REGION_COOKIE_NAME]) && preg_match('/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i', $_COOKIE[CURRENT_REGION_COOKIE_NAME])){
  $region_code = $_COOKIE[CURRENT_REGION_COOKIE_NAME];
  $needSendCookie = false;
}
else{
  $region_code = isset($_SERVER['HTTP_X_GEOIP_REGION']) ? $_SERVER['HTTP_X_GEOIP_REGION'] : null;
}


//формирую условие для определения текущего региона пользователя
if ($region_code)
{
  $is_users_region = ", IF(`geoip_code` = '" . $region_code . "', 1, 0) `is_users_region`";
}
else
{
  $is_users_region = ", `is_default` `is_users_region`";
}

//получаю все доступные регионы с отметкой который из них является текущим у пользователя
$query = "SELECT `id`, `name`, `token`, `geoip_code`" . $is_users_region . " FROM `region` WHERE `is_active` = 1 AND `type` = 'city'";

if (!$result = mysql_query($query, $conn)) {
  die(json_encode(array('success' => false, 'data' => array())));
}

$region = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
  $entity = array(
    'name' => $row['name'],
    'link' => '/region/change/' . $row['token'],
  );
  if ($row['is_users_region']) {
    $entity['is_active'] = 'active';

    //если еще нет в сессии региона, то устанавливаем
    if ($needSendCookie) {
      setcookie(CURRENT_REGION_COOKIE_NAME, $row['geoip_code'], time() + 60 * 60 * 24 * 365, '/');
    }
  }

  $region[] = $entity;
}

mysql_close($conn);

die(json_encode(array('success' => true, 'data' => $region)));