<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->coreV2['url'] = 'http://api.enter.ru/v2/';

$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;

$c->product['globalListEnabled'] = false;


return $c;