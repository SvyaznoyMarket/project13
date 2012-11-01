<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

//$c->coreV2['url'] = 'http://api.enter.ru/v2/';
//$c->coreV2['url'] = 'http://test2.core.ent3.ru/v2/';
$c->coreV2['url'] = 'http://core.ent3.ru/v2/';

$c->coreV1['url'] = 'http://core.ent3.ru/v1/json';

$c->smartEngine['pull'] = true;
$c->smartEngine['push'] = false;

$c->warranty['enabled'] = true;

$c->product['globalListEnabled'] = false;


return $c;