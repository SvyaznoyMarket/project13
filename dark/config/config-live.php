<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->coreV2['url'] = 'http://api.enter.ru/v2/';

$c->wordpress['url'] = 'http://content.enter.ru/';

$c->analytics['enabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;

$c->database['host']     = '10.20.33.2';

return $c;