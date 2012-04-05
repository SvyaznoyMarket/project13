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

//стартую сессию symfony
session_name(SESSION_NAME);
session_start();

$sessionCartIndex = 'cartSoa';

//получаю пользовательские данные из сессии
$user_attributes = isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']) ? $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'] : array();
$user_name = isset($_SESSION['symfony/user/sfUser/attributes']['guard']['user_name']) ? $_SESSION['symfony/user/sfUser/attributes']['guard']['user_name'] : null;
$user_id = isset($_SESSION['symfony/user/sfUser/attributes']['guard']['user_id']) ? $_SESSION['symfony/user/sfUser/attributes']['guard']['user_id'] : null;
$user_id = intval($user_id);
$region_id = isset($user_attributes['region']) ? $user_attributes['region'] : null;

$quantity = 0;

if (isset($user_attributes[$sessionCartIndex]) && isset($user_attributes[$sessionCartIndex]['products'])) {
  foreach ($user_attributes[$sessionCartIndex]['products'] as $product) {
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
if (isset($user_attributes['productCompare']) && isset($user_attributes['productCompare']['products'])) {
  foreach ($user_attributes['productCompare']['products'] as $prod) {
    $vcomp += count($prod);
  }
}

/*
 * тут у нас либо информация есть в сессии, либо ее надо достать из бд
 */
$prod_sum = 0;
$needDb = false;
if (isset($user_attributes[$sessionCartIndex]) && isset($user_attributes[$sessionCartIndex]['products'])) {
  foreach ($user_attributes[$sessionCartIndex]['products'] as $product) {
    if (!isset($product['token']) || !isset($product['total'])) {
      $needDb = true;
      break;
    }
    $prod_sum += $product['total'];
  }
  if ($needDb) {
    $prod_sum = 0;
    //Загружаем из бд, на сайте инфа старая, сессии не наполнены

    $productIdList = array_keys($user_attributes['cart']['products']);
    foreach ($productIdList as $id => $key) {
      $productIdList[$id] = intval($key);
    }
    $query = "SELECT product.token, product_price.product_id, product_price.price, product_price.product_price_list_id
							FROM product_price
							LEFT JOIN product on product_price.product_id = product.id
							where product_id IN(" . implode(", ", $productIdList) . ")
							and product_price_list_id IN (SELECT product_price_list_id from region where id = " . intval($region_id) . " or is_default = 1)
							order by product_price_list_id;";
    if ($result = mysql_query($query, $conn)) {
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        if (isset($user_attributes['cart']['products'][$row['product_id']])) {
          $user_attributes['cart']['products'][$row['product_id']]['token'] = $row['token'];
          $user_attributes['cart']['products'][$row['product_id']]['total'] = $user_attributes['cart']['products'][$row['product_id']]['quantity'] * $row['price'];
          if (isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['products'][$row['product_id']])) {
            $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['products'][$row['product_id']]['token'] = $row['token'];
            $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['products'][$row['product_id']]['total'] = $user_attributes[$sessionCartIndex]['products'][$row['product_id']]['total'];
          }
        }
      }
      foreach ($user_attributes['cart']['products'] as $product) {
        $prod_sum += $product['total'];
      }
    }
    else {
      mysql_close($conn);
      die(json_encode(array('success' => false, 'data' => array())));
    }
  }
}

$service_sum = 0;
$needDb = false;
if (isset($user_attributes[$sessionCartIndex]) && isset($user_attributes[$sessionCartIndex]['services'])) {
  foreach ($user_attributes[$sessionCartIndex]['services'] as $service) {
    if (!isset($service['token']) || !isset($service['total'])) {
      $needDb = true;
      break;
    }
    $service_sum += $service['total'];
  }
  if ($needDb) {
    /**
     * получение полных цен сервисов из бд (если товар был
     * добавлен в корзину давно не по новой схеме наполнения данными)
     */
    $service_sum = 0;
    $serviceWHERE = array();
    foreach ($user_attributes['cart']['services'] as $serviceId => $service) {
      $serviceId = intval($serviceId);
      if ($service['quantity'] > 0) {
        $serviceWHERE[] = "(service.id = {$serviceId} and service_price.product_id is null)";
      }
      if (isset($service['product']) and is_array($service['product']) and count($service['product']) > 0) {
        $keys = array_keys($service['product']);
        foreach ($keys as $id => $key) {
          $keys[$id] = intval($key);
        }
        $serviceWHERE[] = "(service.id = {$serviceId} and (service_price.product_id in (" . implode(", ", $keys) . ")  or service_price.product_id is null))";
      }
    }

    $regionWhere = is_null($region_id) ? 'is_default = 1' : 'id = ' . intval($region_id);

    $query = "select
	              service.id, service.token, service_price.product_id, service_price.price, service_price.core_id
	            from service
							left join service_price
							    on service_price.service_id = service.id
							    and service_price.service_price_list_id = (select product_price_list_id from region where {$regionWhere})
							where " . implode(" OR ", $serviceWHERE) . "
							order by service_price.core_id ASC;";
    //Загружаем из бд, на сайте инфа старая, сессии не наполнены
    $prices = array();
    if ($result = mysql_query($query, $conn)) {
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
      {
        if (isset($user_attributes['cart']['services'][$row['id']])) {
          $user_attributes['cart']['services'][$row['id']]['token'] = $row['token'];
        }

        if (isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['services'][$row['id']])) {
          $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['services'][$row['id']]['token'] = $row['token'];
        }

        if (!isset($prices[$row['id']])) {
          $prices[$row['id']] = array();
        }
        $productId = is_null($row['product_id']) ? 0 : intval($row['product_id']);
        $prices[$row['id']][$productId] = $row['price'];
      }
    }
    else {
      mysql_close($conn);
      die(json_encode(array('success' => false, 'data' => array())));
    }

    /**
     * наполняем сервисы полными ценами
     */
    $servicesTotalPrices = array();
    foreach ($user_attributes['cart']['services'] as $serviceId => $service) {
      $servicesTotalPrices[$serviceId] = $service['quantity'] * $prices[$serviceId][0];
      if (isset($service['product']) and is_array($service['product']) and count($service['product']) > 0) {
        foreach ($service['product'] as $productId => $productQuantity) {
          $servicesTotalPrices[$serviceId] += $productQuantity * (isset($prices[$serviceId][$productId]) ? $prices[$serviceId][$productId] : $prices[$serviceId][0]);
        }
        $service_sum += $servicesTotalPrices[$serviceId];
      }
    }

    foreach ($servicesTotalPrices as $serviceId => $serviceTotalPrice) {
      if (isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['services'][$row['id']])) {
        $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['cart']['services'][$serviceId]['total'] = $serviceTotalPrice;
      }
    }
  }
}

$productsInCart = array();
if (isset($user_attributes[$sessionCartIndex]) && isset($user_attributes[$sessionCartIndex]['products'])) {
  foreach ($user_attributes[$sessionCartIndex]['products'] as $product) {
    $productsInCart[$product['token']] = $product['quantity'];
  }
}
$servicesInCart = array();
if (isset($user_attributes[$sessionCartIndex]) && isset($user_attributes[$sessionCartIndex]['services'])) {
  foreach ($user_attributes[$sessionCartIndex]['services'] as $service) {
    $servicesInCart[$service['token']] = $service['quantity'];
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
  )
);
mysql_close($conn);
die(json_encode($response));