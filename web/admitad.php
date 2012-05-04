<?php

define('SESSION_NAME', 'enter');

$uidName = 'admitad_uid';
$uid = !empty($_GET[$uidName]) ? $_GET[$uidName] : null;

if ($uid) {
  session_name(SESSION_NAME);
  session_start();

  $request = parse_url($_SERVER['REQUEST_URI']);
  $query = array();
  parse_str($request['query'], $query);
  unset($query[$uidName]);

  $location = 'http://'.$_SERVER['HTTP_HOST'].$request['path'].(count($query) > 0 ? ('?'.http_build_query($query)) : '');

  //var_dump($location); exit();
  $_SESSION['symfony/user/sfUser/attributes']['symfony/user/sfUser/attributes']['admitad_uid'] = $uid;

  header ('HTTP/1.1 301 Moved Permanently');
  header ('Location: '.$location);
  exit();
}