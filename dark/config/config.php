<?php

require_once __DIR__ . '/../lib/Config/AppConfig.php';

$c = new \Config\AppConfig();
// encoding
$c->encoding = 'UTF-8';
// dirs
$c->appDir = realpath(__DIR__ . '/..');
$c->configDir = $c->appDir . '/config';
$c->libDir = $c->appDir . '/lib';
$c->dataDir = $c->appDir . '/data';
$c->logDir = realpath($c->appDir . '/../log');
$c->webDir = realpath($c->appDir . '/../web');

$c->debug = true;
$c->appName = 'Enter';
$c->authToken['name']     = 'enter_auth';
$c->authToken['lifetime'] = 15552000; // 180 дней
$c->sessionToken = 'enter';
$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 15552000;

$c->coreV1['url']          = 'http://core.ent3.ru/v1/json';
$c->coreV1['client_id']    = 'site';
$c->coreV1['consumer_key'] = 'test';
$c->coreV1['signature']    = 'test';

$c->coreV2['url']       = 'http://core.ent3.ru/index.php/v2/';
$c->coreV2['client_id'] = 'site';


$c->wordpress['url'] = 'http://content.ent3.ru/';

$c->company['phone'] = '8 (800) 700-00-09';

$c->googleAnalytics['enabled'] = false;

$c->yandexMetrika['enabled'] = false;

$c->asset['timestampEnabled'] = true;

$c->onlineCall['enabled'] = false;

$c->region['cookieName']     = 'geoshop';
$c->region['cookieLifetime'] = 31536000; // 365 дней
$c->region['defaultId']      = 14974;

$c->mediaHost = array(
    0 => 'http://fs01.enter.ru',
    1 => 'http://fs02.enter.ru',
    2 => 'http://fs03.enter.ru',
    3 => 'http://fs04.enter.ru',
    4 => 'http://fs05.enter.ru',
    5 => 'http://fs06.enter.ru',
    6 => 'http://fs07.enter.ru',
    7 => 'http://fs08.enter.ru',
    8 => 'http://fs09.enter.ru',
    9 => 'http://fs10.enter.ru',
);

$c->product['itemsPerPage']          = 18;
$c->product['showAccessories']       = true;
$c->product['showRelated']           = true;
$c->product['itemsInSlider']         = 5;
$c->product['itemsInCategorySlider'] = 3;
$c->product['minCreditPrice']        = 3000;
$c->product['totalCount']            = 30000;
// глобальный (без учета региона) список товаров
$c->product['globalListEnabled']     = false;

$c->productPhoto['url'] = array(
    0 => '/1/1/60/',
    1 => '/1/1/120/',
    2 => '/1/1/163/',
    3 => '/1/1/500/',
    4 => '/1/1/2500/',
);

$c->productPhoto3d['url'] = array(
    0 => '/1/2/500/',
    1 => '/1/2/2500/',
);

$c->productLabel['url'] = array(
    0 => 'http://fs01.enter.ru/7/1/66x23/',
    1 => 'http://fs01.enter.ru/7/1/124x38/',
);

$c->productCategory['url'] = array(
    0 => 'http://fs01.enter.ru/6/1/163/',
);

$c->service['url'] = array(
    0 => 'http://fs01.enter.ru/11/1/160/',
    1 => 'http://fs01.enter.ru/11/1/500/',
    2 => 'http://fs01.enter.ru/11/1/120/',
);
$c->service['minPriceForDelivery'] = 950;

$c->shopPhoto['url'] = array(
    0 => 'http://fs01.enter.ru/8/1/40/',
    1 => 'http://fs01.enter.ru/8/1/120/',
    2 => 'http://fs01.enter.ru/8/1/163/',
    3 => 'http://fs01.enter.ru/8/1/500/',
    4 => 'http://fs01.enter.ru/8/1/2500/',
    5 => 'http://fs01.enter.ru/8/1/original/',
);

$c->payment['creditEnabled'] = true;

$c->smartEngine['pull'] = true;
$c->smartEngine['push'] = true;

$c->warranty['enabled'] = false;


return $c;