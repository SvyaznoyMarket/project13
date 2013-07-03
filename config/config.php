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
$c->cmsDir = $c->appDir . '/../../cms.enter.ru/wwwroot';

$c->controllerPrefix = 'Controller';
$c->routePrefix = '';

$c->debug = false;
$c->appName = 'Enter';
$c->authToken['name']     = '_token';
$c->sessionToken = 'enter';

$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 15552000;

$c->cacheCookieName = 'enter_auth';

$c->mainHost = 'www.enter.ru';
$c->mobileHost = 'm.enter.ru';

$c->coreV2['url']          = 'http://api.enter.ru/v2/';
$c->coreV2['client_id']    = 'site';
$c->coreV2['timeout']      = null;
$c->coreV2['retryCount']   = 3;
$c->coreV2['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->corePrivate['url']          = 'http://api.enter.ru/private/';
$c->corePrivate['user']         = 'Developer';
$c->corePrivate['password']     = 'dEl23sTOas';
$c->corePrivate['timeout']      = null;
$c->corePrivate['retryCount']   = 3;
$c->corePrivate['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->reviewsStore['url']          = 'http://reviews.enter.ru/reviews/';
$c->reviewsStore['retryCount']   = 2;
$c->reviewsStore['timeout']      = 0.36;
$c->reviewsStore['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
    'forever' => 0,
];

$c->wordpress['url'] = 'http://content.enter.ru/';
$c->wordpress['timeout'] = 2;
$c->wordpress['throwException'] = true;

$c->dataStore['url'] = 'http://cms.enter.ru/v1/';
$c->dataStore['timeout'] = 0.25;
$c->dataStore['retryCount'] = 3;
$c->dataStore['retryTimeout'] = [
    'default' => 0.1,
    'tiny'    => 0.01,
    'short'   => 0.05,
    'medium'  => 0.1,
    'long'    => 0.2,
    'huge'    => 0.5,
    'forever' => 0,
];

$c->company['phone'] = '8 (800) 700-00-09';
$c->company['moscowPhone'] = '8 (495) 775-00-06';
$c->company['icq'] = '648198963';

$c->analytics['enabled'] = true;
$c->analytics['optimizelyEnabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;
$c->adFox['enabled'] = true;
$c->myThings['feeByCategory'] = [
    80      => 0.105,   //Мебель
    224     => 0.065,   //Сделай сам
    1438    => 0.06,    //Зоотовары
    320     => 0.07,    //Детские товары
    443     => 0.105,   //Товары для дома
    788     => 0.05,       //Электроника
    //1024    => 0.05,    //Электроника => Аксессуары
    1       => 0.038,   //Бытовая техника
    21      => 0.064,   //Красота и здоровье
    923     => 0.123,   //Украшения и часы
    2545    => 0.061,   //Парфюмерия и косметика
    185     => 0.098,   //Подарки и хобби
    647     => 0.114,   //Спорт и отдых
    225     => 0.065,   //Аксессуары для авто
];

$c->myThings['cookieName'] = '_mythings';

$c->partner['cookieName'] = '_partner';
$c->partner['cookieLifetime'] = 2592000; // 30 дней

$c->onlineCall['enabled'] = false;

// промсвязьбанк
$c->paymentPsb['terminal']     = '20097201';
$c->paymentPsb['merchant']     = '000541120097201';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key']          = 'FD5AAE47574BB8EEFBF8C6C14CBCA453';
$c->paymentPsb['payUrl']       = 'https://3ds.payment.ru/cgi-bin/cgi_link';

// промсвязьбанк invoice
$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key'] = $c->dataDir . '/key/live.privkey.pem';
$c->paymentPsbInvoice['payUrl'] = 'https://retail.payment.ru/invoice.aspx';

$c->region['cookieName']     = 'geoshop';
$c->region['cookieLifetime'] = 31536000; // 365 дней
$c->region['defaultId']      = 14974;
$c->region['autoresolve']    = true;

$c->loadMediaHost = true;
$c->shop['cookieName'] = 'shopid';
$c->shop['cookieLifetime'] = 31536000; // 365 дней
$c->shop['autoresolve']    = true;
$c->shop['enabled'] = true;

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

$c->product['itemsPerPage']           = 18;
$c->product['showAccessories']        = true;
$c->product['showRelated']            = true;
$c->product['itemsInSlider']          = 5;
$c->product['itemsInCategorySlider']  = 3;
$c->product['itemsInAccessorySlider'] = 4;
$c->product['minCreditPrice']         = 3000;
$c->product['totalCount']             = 30000;
// глобальный (без учета региона) список товаров
$c->product['globalListEnabled']      = true;
$c->product['showAveragePrice']       = false;
$c->product['allowBuyOnlyInshop']     = false;
$c->product['reviewEnabled']          = true;
$c->product['lowerPriceNotification'] = false;
$c->product['furnitureConstructor']   = true;
// jewel
$c->product['itemsPerPageJewel']     = 24;
$c->product['itemsPerRowJewel']      = 4;

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
    0 => '/7/1/66x23/',
    1 => '/7/1/124x38/',
];

$c->productCategory['url'] = [
    0 => '/6/1/163/',
];
$c->productCategory['jewelController'] = true;

$c->service['url'] = [
    0 => '/11/1/160/',
    1 => '/11/1/500/',
    2 => '/11/1/120/',
];

$c->serviceCategory['url'] = [
    0 => '/10/1/160/',
    1 => '/10/1/500/',
];

$c->service['minPriceForDelivery'] = 950;

$c->shopPhoto['url'] = [
    0 => '/8/1/40/',
    1 => '/8/1/120/',
    2 => '/8/1/163/',
    3 => '/8/1/500/',
    4 => '/8/1/2500/',
    5 => '/8/1/original/',
];

$c->banner['timeout'] = 6000;
$c->banner['url'] = [
    0 => '/4/1/230x302/',
    1 => '/4/1/768x302/',
    2 => '/4/1/920x320/',
];

$c->cart['productLimit'] = 50;

$c->payment['creditEnabled'] = true;

$c->smartengine['pull']           = true;
$c->smartengine['push']           = true;
$c->smartengine['cert']           = $c->dataDir . '/cert/gsorganizationvalg2.crt';
$c->smartengine['apiUrl']         = 'https://www.selightprod.smartengine.at/se-light/api/1.0/json/';
$c->smartengine['apiKey']         = 'c41851b19511c20acc84f47b7816fb8e';
$c->smartengine['tenantid']       = 'ENojUTRcD8';
$c->smartengine['logEnabled']     = true;
$c->smartengine['logDataEnabled'] = true;
$c->smartengine['sslVerify']      = true;

$c->crossss['enabled'] = true;
$c->crossss['timeout'] = 0.3;
$c->crossss['apiUrl']  = 'http://crossss.com/api.ashx';
$c->crossss['id']      = 45;
$c->crossss['apiKey']  = '5a0bb0cb92a94f7db8a9bf4bfacdbe39';

$c->warranty['enabled'] = true;
$c->f1Certificate['enabled'] = true;
$c->coupon['enabled'] = true;

$c->user['corporateRegister'] = true;

$c->database['host']     = 'site-db';
$c->database['name']     = 'enter';
$c->database['user']     = 'root';
$c->database['password'] = 'qazwsxedc';

$c->creditProvider['kupivkredit']['partnerId'] = '1-6ADAEAT';
$c->creditProvider['kupivkredit']['partnerName'] = 'ООО «Enter»';
$c->creditProvider['kupivkredit']['signature'] = 'enter-secret-werj7537';

$c->queue['pidFile'] = (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid';
$c->queue['workerLimit'] = 10;
$c->queue['maxLockTime'] = 600;

$c->abtest['cookieName'] = 'switch';
$c->abtest['bestBefore'] = '2013-04-23';
$c->abtest['enabled']    = true;
$c->abtest['test']       = [
    [
        'traffic'  => '40',
        'key'      => 'upsell',
        'name'     => 'Страница tocart',
        'ga_event' => 'tocart',
    ],
    [
        'traffic'  => '40',
        'key'      => 'order2cart',
        'name'     => 'Страница cart',
        'ga_event' => 'cart',
    ],
];

$c->subscribe['enabled'] = true;
$c->subscribe['cookieName'] = 'subscribed';

$c->requestMainMenu = true;

$c->mobileModify['enabled'] = true;

$c->order['enableMetaTag'] = true;

$c->maybe3d['xmlUrl']     = 'http://hq.maybe3d.com/MappingService.svc/GetMappings?customerId=';
$c->maybe3d['customerId'] = 'BE2016EF-32D8-41E6-976F-A8D32EB20ACF';
$c->maybe3d['swfUrl']     = 'http://fs01.enter.ru/3d/flash/';
$c->maybe3d['cmsFolder']  = '/opt/wwwroot/cms.enter.ru/wwwroot/v1/video/product/';
$c->maybe3d['timeout']    = 30;

return $c;