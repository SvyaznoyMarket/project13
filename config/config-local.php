<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config-dev.php';

//$c->coreV2['url'] = 'http://enter-core.loc/v2/';
//$c->coreV2['url'] = 'http://test2.core.ent3.ru/v2/';
//$c->coreV2['url'] = 'http://api.enter.ru/v2/';

$c->analytics['enabled'] = false;

$c->mobileHost = 'enter-mobile.loc';

$c->warranty['enabled'] = true;

return $c;