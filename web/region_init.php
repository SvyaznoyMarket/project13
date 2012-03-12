<?php

  $sessionName = 'enter';
  $dbName = 'enter';
  $dbUser = 'root';
  $dbPassword = 'qazwsxedc';
  $dbHost = '10.20.33.2';
  $cookieGeoipName = 'geoshop';

  if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')
  {
    header('HTTP/1.0 404 Not Found');
    require('../apps/main/modules/default/templates/error404Success.php');
    exit();
  }

  //устанавливаю заголовок ответа json
  header('Content-Type:	application/json');

  //стартую сессию symfony
  session_name($sessionName);
  session_start();

  //подключаюсь к базе
  if (! ($conn = mysql_connect($dbHost, $dbUser, $dbPassword)))
  {
    die(json_encode(array('success' => false, 'data' => array())));
  }

  //переключаюсь на нужную базу
  if (!mysql_select_db($dbName, $conn))
  {
    die(json_encode(array('success' => false, 'data' => array())));
  }

  //получаю пользовательские данные из сессии
  $user_attributes = isset($_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']) ? $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes'] : array();

  //получаю id региона пользователя
  $region_id = isset($user_attributes['region']) ? $user_attributes['region'] : null;

  //если id региона еще нет, пытаюсь получить geoip_code из серверного окружения
  if (!$region_id)
  {
    $region_code = isset($_SERVER['HTTP_X_GEOIP_REGION']) ? $_SERVER['HTTP_X_GEOIP_REGION'] : null;
  }
  else
  {
    $region_code = null;
  }

  //формирую условие для определения текущего региона пользователя
  if ($region_id)
  {
    $is_users_region = ", IF(`id` = '.$region_id.', 1, 0) `is_users_region`";
  }
  elseif ($region_code)
  {
    $is_users_region = ", IF(`geoip_code` = '".$region_code."', 1, 0) `is_users_region`";
  }
  else
  {
    $is_users_region = ", `is_default` `is_users_region`";
  }

  //получаю все доступные регионы с отметкой который из них является текущим у пользователя
  $query = "SELECT `id`, `name`, `token`, `geoip_code`".$is_users_region." FROM `region` WHERE `is_active` = 1 AND `type` = 'city'";

  if (!$result = mysql_query($query, $conn))
  {
    die(json_encode(array('success' => false, 'data' => array())));
  }

  $region = array();
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    $entity = array(
      'name' => $row['name'],
      'link' => '/region/change/'.$row['token'],
    );
    if ($row['is_users_region'])
    {
      $entity['is_active'] = 'active';

      //если еще нет в сессии региона, то устанавливаем
      if (!$region_id)
      {
        $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['region'] = $row['id'];
        setcookie($cookieGeoipName, $row['geoip_code'], time() + 60 * 60 * 24 * 365);
      }
    }

    $region[] = $entity;
  }

  mysql_close($conn);

  die(json_encode(array('success' => true, 'data' => $region)));