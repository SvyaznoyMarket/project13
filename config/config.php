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
$c->logDir = $c->appDir . '/log';
$c->webDir = $c->appDir . '/web';
$c->templateDir = $c->appDir . '/main/template';

$c->controllerPrefix = 'Controller';
$c->routePrefix = '';

$c->debug = true;
$c->appName = 'Enter';
$c->authToken['name']     = '_token';
$c->sessionToken = 'enter';
$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 15552000;

$c->cacheCookieName = 'enter_auth';

$c->coreV2['url']       = 'http://core.ent3.ru/v2/';
$c->coreV2['client_id'] = 'site';
$c->coreV2['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.1,
    'short'   => 0.4,
    'medium'  => 1,
    'long'    => 1.6,
    'huge'    => 3,
    'forever' => 0,
];
$c->coreV2['retryCount'] = 3;

$c->wordpress['url'] = 'http://content.ent3.ru/';

$c->dataStore['url'] = 'http://cms.enter.ru/';
$c->dataStore['timeout'] = 3;
$c->dataStore['retryTimeout'] = [
    'default' => 0.05,
    'tiny'    => 0.1,
    'short'   => 0.4,
    'medium'  => 1,
    'long'    => 1.6,
    'huge'    => 3,
    'forever' => 0,
];
$c->dataStore['retryCount'] = 3;

$c->company['phone'] = '8 (800) 700-00-09';
$c->company['moscowPhone'] = '8 (495) 775-00-06';
$c->company['icq'] = '648198963';

$c->adFox['enabled'] = false;
$c->analytics['enabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->myThings['feeByCategory'] = [
    80      => 0.05,
    224     => 0.03,
    1438    => 0.04,
    320     => 0.05,
    443     => 0.05,
    788     => 0.015,
    1       => 0.02,
    21      => 0.05,
    923     => 0.06,
    2545    => 0.03,
    185     => 0.03,
    647     => 0.05,
    225     => 0.03,
];

$c->onlineCall['enabled'] = false;

$c->region['cookieName']     = 'geoshop';
$c->region['cookieLifetime'] = 31536000; // 365 дней
$c->region['defaultId']      = 14974;

$c->mediaHost = [
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
];

$c->search['itemLimit'] = 1000;

$c->product['itemsPerPage']          = 18;
$c->product['showAccessories']       = true;
$c->product['showRelated']           = true;
$c->product['itemsInSlider']         = 5;
$c->product['itemsInCategorySlider'] = 3;
$c->product['minCreditPrice']        = 3000;
$c->product['totalCount']            = 30000;
// глобальный (без учета региона) список товаров
$c->product['globalListEnabled']     = true;
$c->product['showAveragePrice']      = false;

$c->productPhoto['url'] = [
    0 => '/1/1/60/',
    1 => '/1/1/120/',
    2 => '/1/1/163/',
    3 => '/1/1/500/',
    4 => '/1/1/2500/',
];

$c->productPhoto3d['url'] = [
    0 => '/1/2/500/',
    1 => '/1/2/2500/',
];

$c->productLabel['url'] = [
    0 => 'http://fs01.enter.ru/7/1/66x23/',
    1 => 'http://fs01.enter.ru/7/1/124x38/',
];

$c->productCategory['url'] = [
    0 => 'http://fs01.enter.ru/6/1/163/',
];

$c->service['url'] = [
    0 => 'http://fs01.enter.ru/11/1/160/',
    1 => 'http://fs01.enter.ru/11/1/500/',
    2 => 'http://fs01.enter.ru/11/1/120/',
];

$c->serviceCategory['url'] = [
    0 => 'http://fs01.enter.ru/10/1/160/',
    1 => 'http://fs01.enter.ru/10/1/500/',
];

$c->service['minPriceForDelivery'] = 950;

$c->shopPhoto['url'] = [
    0 => 'http://fs01.enter.ru/8/1/40/',
    1 => 'http://fs01.enter.ru/8/1/120/',
    2 => 'http://fs01.enter.ru/8/1/163/',
    3 => 'http://fs01.enter.ru/8/1/500/',
    4 => 'http://fs01.enter.ru/8/1/2500/',
    5 => 'http://fs01.enter.ru/8/1/original/',
];

$c->banner['timeout'] = 6000;
$c->banner['url'] = [
    0 => 'http://fs01.enter.ru/4/1/230x302/',
    1 => 'http://fs01.enter.ru/4/1/768x302/',
    2 => 'http://fs01.enter.ru/4/1/920x320/',
];

$c->cart['productLimit'] = 50;

$c->payment['creditEnabled'] = true;

$c->smartEngine['pull'] = true;
$c->smartEngine['push'] = true;
$c->smartEngine['api_url'] = 'https://www.selightprod.smartengine.at/se-light/api/1.0/json/';
$c->smartEngine['api_key'] = 'c41851b19511c20acc84f47b7816fb8e';
$c->smartEngine['tenantid'] = 'ENojUTRcD8';
$c->smartEngine['log_enabled'] = true;
$c->smartEngine['log_data_enabled'] = true;
$c->smartEngine['ssl_verify'] = true;

$c->warranty['enabled'] = true;

$c->user['corporateRegister'] = true;

$c->database['host']     = 'localhost';
$c->database['name']     = 'enter';
$c->database['user']     = 'root';
$c->database['password'] = 'qazwsxedc';

$c->creditProvider['kupivkredit']['partnerId'] = '1-6ADAEAT';
$c->creditProvider['kupivkredit']['partnerName'] = 'ООО «Enter»';
$c->creditProvider['kupivkredit']['signature'] = 'enter-secret-werj7537';

$c->queue['pidFile'] = (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid';
$c->queue['workerLimit'] = 10;
$c->queue['maxLockTime'] = 600;

$c->abtest = [
    'cookieName' => 'switch',
    'bestBefore' => '2013-01-01', //кука умрет в 00:00
    'enabled'    => false,
    'test'       => [],
];

return $c;