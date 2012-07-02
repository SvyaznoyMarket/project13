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
define('NEW_VERSION_CART_NAME', 'cartSoa');
define('OLD_VERSION_CART_NAME', 'cart');
define('DEFAULT_REGION_ID', 19355);
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
  error($e);
}

class DB
{
  private static $dbConn = NULL;

  public static function set($conn)
  {
    self::$dbConn = $conn;
  }

  public static function get()
  {
    return self::$dbConn;
  }
}

DB::set($conn);

function moveOldDataToNewFormat($oldFormatData, &$newFormatDataContainer, $region_id)
{
  /*
    * Перенос продуктов
    */
  if (isset($oldFormatData['products']) && count($oldFormatData['products'])) {
    $needProductInfo = array_keys($oldFormatData['products']);
    foreach ($needProductInfo as $key => $productId) {
      if (isset($newFormatDataContainer[$productId])) {
        $newFormatDataContainer['products'][$productId]['quantity'] += $oldFormatData['products'][$productId]['quantity'];
        unset($needProductInfo[$key]);
      }
      else {
        $needProductInfo[$key] = intval($productId);
      }
    }
    if (count($needProductInfo)) {
      $productInfoList = getProductInfoByIds($needProductInfo, $region_id);
      foreach ($productInfoList as $productId => $productInfo) {
        $newFormatDataContainer['products'][$productId] = $productInfo;
        $newFormatDataContainer['products'][$productId]['quantity'] = $oldFormatData['products'][$productId]['quantity'];
      }
    }
  }

  /*
    * Перенос услуг
    */
  if (isset($oldFormatData['services'])) {
    $needServiceProductPrice = array();
    foreach ($oldFormatData['services'] as $service_id => $service) {
      if (isset($service['products']) && is_array($service['products'])) {
        foreach ($service['products'] as $product_id => $productQuantity) {
          if (isset($newFormatDataContainer['services'][$service_id]['products'][$product_id])) {
            $newFormatDataContainer['services'][$service_id]['products'][$product_id] += $productQuantity;
          }
          else {
            if (!isset($needServiceProductPrice[$service_id])) {
              $needServiceProductPrice[$service_id] = array();
            }
            $needServiceProductPrice[$service_id][$product_id] = $productQuantity;
          }
        }
      }
      if (intval($service['quantity']) > 0) {
        $needServiceProductPrice[$service_id]['0'] = $service['quantity'];
      }
    }

    $serviceInfo = getServiceProductInfo($needServiceProductPrice, $region_id);
    $priceList = $serviceInfo['prices'];
    $tokenList = $serviceInfo['tokens'];

    foreach ($oldFormatData['services'] as $service_id => $service) {
      if (!isset($newFormatDataContainer['services'][$service_id])) {
        $newFormatDataContainer['services'][$service_id] = array();
      }
      $newFormatDataContainer['services'][$service_id]['id'] = $service_id;
      $newFormatDataContainer['services'][$service_id]['token'] = $tokenList[$service_id];
      if (!isset($newFormatDataContainer['services'][$service_id]['products'])) {
        $newFormatDataContainer['services'][$service_id]['products'] = array();
      }

      if (intval($service['quantity']) > 0) {
        $newFormatDataContainer['services'][$service_id]['products']['0'] = array(
          'quantity' => $service['quantity'],
          'price' => $priceList[$service_id][0]
        );
      }
      if (isset($service['products']) && is_array($service['products'])) {
        foreach ($service['products'] as $productId => $productQuantity) {
          $price = isset($priceList[$service_id][$productId]) ? $priceList[$service_id][$productId] : $priceList[$service_id][0];

          $newFormatDataContainer['services'][$service_id]['products'][$productId] = array(
            'quantity' => $productQuantity,
            'price' => $price
          );
        }
      }
    }
  }
}

function getProductInfoByIds($idList = array(), $region_id)
{
  $conn = DB::get();

  if (is_null($conn)) {
    throw new Exception('db connection is lost');
  }

  $query = "SELECT product.token, product_price.product_id, product_price.price, product_price.product_price_list_id
							FROM product_price
							LEFT JOIN product on product_price.product_id = product.id
							where product_id IN(" . implode(", ", $idList) . ")
							and product_price_list_id IN (SELECT product_price_list_id from region where id = " . intval($region_id) . " or is_default = 1)
							order by product_price_list_id;";

  $return = array();
  if ($result = mysql_query($query, $conn)) {
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      if (isset($user_attributes['cart']['products'][$row['product_id']])) {
        $return[$row['id']] = array(
          'id' => $row['id'],
          'token' => $row['token'],
          'price' => $row['price'],
        );
      }
    }
  }
  else {
    throw new Exception('cant query db');
  }

  return $return;
}

function get_name_by_id($id)
{
  $conn = DB::get();
  $query = 'SELECT first_name,last_name, middle_name  FROM guard_user WHERE id="' . $id . '"';
  if ($result = mysql_query($query, $conn)) {
    $userData = mysql_fetch_array($result, MYSQL_ASSOC);
    if (!$userData) {
      return null;
    }
    $name = '';
    if (isset($userData['first_name'])) {
      $name .= ' ' . $userData['first_name'];
    }
    if (isset($userData['middle_name'])) {
      $name .= ' ' . $userData['middle_name'];
    }
    if (isset($userData['last_name'])) {
      $name .= ' ' . $userData['last_name'];

    }
    return $name;
  }
  else {
    throw new Exception('cant query db');
  }
}

function getServiceProductInfo($serviceInfo, $region_id)
{
  $conn = DB::get();

  if (is_null($conn)) {
    throw new Exception('db connection is lost');
  }

  $serviceWHERE = array();
  foreach ($serviceInfo as $serviceId => $service) {
    $serviceId = intval($serviceId);
    $productList = array();
    foreach ($service as $productId => $productQuantity) {
      $productId = intval($productId);
      if ($productId == 0) {
        $serviceWHERE[] = "(service.id = {$serviceId} and service_price.product_id is null)";
      }
      else {
        $productList[] = $productId;
      }
    }
    if (count($productList) > 0) {
      $serviceWHERE[] = "(service.id = {$serviceId} and (service_price.product_id in (" . implode(", ", $productList) . ")  or service_price.product_id is null))";
    }
  }

  $query = "select
	              service.id, service.token, service_price.product_id, service_price.price, service_price.core_id
	            from service
							left join service_price
							    on service_price.service_id = service.id
							    and service_price.service_price_list_id = (select product_price_list_id from region where id = {$region_id})
							where " . implode(" OR ", $serviceWHERE) . "
							order by service_price.core_id ASC;";

  $prices = array();
  $tokens = array();
  if ($result = mysql_query($query, $conn)) {
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
      if (!isset($prices[$row['id']])) {
        $prices[$row['id']] = array();
      }
      $productId = is_null($row['product_id']) ? 0 : intval($row['product_id']);
      $prices[$row['id']][$productId] = $row['price'];
      if (!isset($tokens[$row['id']])) {
        $tokens[$row['id']] = $row['token'];
      }
    }
  }
  else {
    throw new Exception('cant query db');
  }

  foreach ($serviceInfo as $serviceId => $productList) {
    foreach ($productList as $productId => $productQuantity) {
      if (isset($prices[$serviceId][$productId])) {
        $serviceInfo[$serviceId][$productId] = $prices[$serviceId][$productId];
      }
      else {
        $serviceInfo[$serviceId][$productId] = $prices[$serviceId][0];
      }
    }
  }
  return array('prices' => $serviceInfo, 'tokens' => $tokens);
}

function error($message)
{
  //	echo $message;
  //	echo "\r\n\r\n";
  die(json_encode(array('success' => false, 'data' => array())));
}

//стартую сессию symfony
session_name(SESSION_NAME);
session_start();

if (!isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][NEW_VERSION_CART_NAME])) {
  $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][NEW_VERSION_CART_NAME] = array();
}

//получаю пользовательские данные из сессии
$user_attributes = isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']) ? $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'] : array();
$user_id = isset($_SESSION['symfony/user/sfUser/attributes']['guard']['user_id']) ? $_SESSION['symfony/user/sfUser/attributes']['guard']['user_id'] : null;
$user_id = intval($user_id);

if ($user_id > 0) {
  $user_name = get_name_by_id($user_id);
} else {
  $user_name = null;
}

if(isset($_COOKIE[CURRENT_REGION_COOKIE_NAME]) && preg_match('/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i', $_COOKIE[CURRENT_REGION_COOKIE_NAME])){
  $query = "SELECT id
            FROM `region`
            WHERE `core_id` = {$_COOKIE[CURRENT_REGION_COOKIE_NAME]}  OR `is_default` = 1
            order by is_default ASC
            LIMIT 1";

}
else{
  $query = "SELECT id FROM `region` WHERE `is_default` = 1 LIMIT 1";
}
if ($result = mysql_query($query, $conn)) {
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  $region_id = $row['id'];
}
else {
  $region_id = DEFAULT_REGION_ID;
}

if (isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][OLD_VERSION_CART_NAME])) {
  try {
    moveOldDataToNewFormat($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][OLD_VERSION_CART_NAME], $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][NEW_VERSION_CART_NAME], $region_id);
    $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][OLD_VERSION_CART_NAME] = array();
    unset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][OLD_VERSION_CART_NAME]);
  }
  catch (Exception $e) {
    mysql_close($conn);
    error($e);
  }
}

$sessionCartData = $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'][NEW_VERSION_CART_NAME];

$quantity = 0;
if (isset($sessionCartData['products'])) {
  foreach ($sessionCartData['products'] as $product) {
    $quantity += $product['quantity'];
  }
}

//Из бд количество отложенных товаров
$delayed_product_cnt = 0;
if ($user_id > 0) {
  $query = "SELECT count(*) as cnt FROM `user_delayed_product` WHERE `user_id` = {$user_id}";
  if ($result = mysql_query($query, $conn)) {
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $delayed_product_cnt = $row['cnt'];
  }
}

//ProductsCompare из сессии:
$vcomp = 0;

$prod_sum = 0;
if (isset($sessionCartData['products'])) {
  foreach ($sessionCartData['products'] as $product) {
    $prod_sum += $product['quantity'] * $product['price'];
  }
}

$service_sum = 0;
if (isset($sessionCartData['services'])) {
  foreach ($sessionCartData['services'] as $service) {
    foreach ($service['products'] as $product) {
      $service_sum += ($product['quantity'] * $product['price']);
    }
  }
}

$productsInCart = array();
if (isset($sessionCartData['products'])) {
  foreach ($sessionCartData['products'] as $product) {
    $productsInCart[$product['token']] = $product['quantity'];
  }
}

$servicesInCart = array();
if (isset($sessionCartData['services'])) {
  foreach ($sessionCartData['services'] as $serviceId => $service) {
    if (count($service['products']) < 1) {
      continue;
    }
    $serviceToken = $service['token'];
    $servicesInCart[$serviceToken] = array();
    foreach ($service['products'] as $productId => $product) {
      if ($productId == 0) {
        $servicesInCart[$serviceToken]['0'] = $product['quantity'];
      }
      else {
        $productToken = $sessionCartData['products'][$productId]['token'];
        $servicesInCart[$serviceToken][$productToken] = $product['quantity'];
      }
    }
  }
}

$response = array(
  'success' => true,
  'data' => array(
    'name' => htmlspecialchars($user_name, ENT_QUOTES),
    'link' => '/private/', //ссылка на личный кабинет
    'vitems' => $quantity,
    'sum' => ($prod_sum + $service_sum),
    'vwish' => $delayed_product_cnt,
    'vcomp' => $vcomp,
    'productsInCart' => $productsInCart,
    'servicesInCart' => $servicesInCart,
    'bingo' => false,
    'region_id' =>$region_id
  )
);
mysql_close($conn);
die(json_encode($response));