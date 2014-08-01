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
$c->logDir = realpath($c->appDir . '/../logs');
$c->webDir = $c->appDir . '/web';
$c->templateDir = $c->appDir . '/main/template';
$c->cmsDir = $c->appDir . '/../../cms.enter.ru/wwwroot';

$c->controllerPrefix = 'Controller';
$c->routePrefix = '';

$c->debug = false;
$c->logger['pretty'] = false;
$c->appName = 'Enter';
$c->authToken['name']     = '_token';
$c->authToken['authorized_cookie'] = '_authorized';

$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 15552000;
$c->session['cookie_domain'] = '.enter.ru';

$c->cacheCookieName = 'enter_auth'; //TODO: удалить

$c->mainHost = 'www.enter.ru';
$c->mobileHost = 'm.enter.ru';

$c->redirect301['enabled'] = true;
$c->mobileRedirect['enabled'] = false;

$c->coreV2['url']          = 'http://api.enter.ru/v2/';
$c->coreV2['client_id']    = 'site';
$c->coreV2['timeout']      = 5;
$c->coreV2['hugeTimeout']  = 90;
$c->coreV2['retryCount']   = 2;
$c->coreV2['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
    'forever' => 0,
];
$c->coreV2['chunk_size']   = 50;
$c->coreV2['debug']        = false;

$c->corePrivate['url']          = 'http://api.enter.ru/private/';
$c->corePrivate['user']         = 'Developer';
$c->corePrivate['password']     = 'dEl23sTOas';
$c->corePrivate['timeout']      = 5;
$c->corePrivate['retryCount']   = 2;
$c->corePrivate['retryTimeout'] = [
    'default' => 1.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->reviewsStore['url']          = 'http://scms.enter.ru/reviews/';
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
$c->wordpress['timeout'] = 1.8;
$c->wordpress['throwException'] = true;
$c->wordpress['retryCount'] = 2;
$c->wordpress['retryTimeout'] = [
    'default' => 0.3,
    'tiny'    => 0.1,
    'short'   => 0.2,
    'medium'  => 0.3,
    'long'    => 0.5,
    'huge'    => 1,
    'forever' => 0,
];

$c->dataStore['url'] = 'http://cms.enter.ru/v1/';
$c->dataStore['timeout'] = 0.8;
$c->dataStore['retryCount'] = 2;
$c->dataStore['retryTimeout'] = [
    'default' => 0.04,
    'tiny'    => 0.04,
    'short'   => 0.08,
    'medium'  => 0.1,
    'long'    => 0.5,
    'huge'    => 1,
    'forever' => 0,
];

$c->scmsV2['url']          = 'http://scms.enter.ru/v2/';
$c->scmsV2['retryCount']   = 2;
$c->scmsV2['timeout']      = 0.36;
$c->scmsV2['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
    'forever' => 0,
];

$c->connectTerminal = true;

$c->company['phone'] = '8 (800) 700-00-09';
$c->company['moscowPhone'] = '8 (495) 775-00-06';
$c->company['icq'] = '648198963';

$c->jsonLog['enabled'] = true;
$c->analytics['enabled'] = true;
$c->analytics['optimizelyEnabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;
$c->kissmentrics['enabled'] = true;
$c->kissmentrics['cookieName']['needUpdate'] = 'kissNeedUpdate';
$c->googleTagManager['enabled'] = true;
$c->googleTagManager['containerId'] = 'GTM-P65PBR';

$c->pickpoint['url'] = 'http://e-solution.pickpoint.ru/api/';
$c->pickpoint['timeout'] = 60;
$c->pickpoint['retryCount'] = 3;
$c->pickpoint['retryTimeout'] = [
    'default' => 0.04,
    'tiny'    => 0.04,
    'short'   => 0.08,
    'medium'  => 0.1,
    'long'    => 0.5,
    'huge'    => 1,
    'forever' => 0,
];

$c->shopScript['enabled'] = true;
$c->shopScript['url'] = 'http://admin.enter.ru/v2/';
//$c->shopScript['user'] = 'admin';
//$c->shopScript['password'] = 'booToo9x';
$c->shopScript['timeout'] = 3;
$c->shopScript['retryCount'] = 2;
$c->shopScript['retryTimeout'] = [
    'default' => 0.3,
    'tiny'    => 0.1,
    'short'   => 0.2,
    'medium'  => 0.3,
    'long'    => 0.5,
    'huge'    => 1,
    'forever' => 0,
];


// TODO: Вынести сюда же настройки для sociomantic and get4click
$c->partners['criteo']['enabled'] = true;
$c->partners['criteo']['account'] = 10442;

// RetailRocket
$c->partners['RetailRocket']['account'] = '519c7f3c0d422d0fe0ee9775';
$c->partners['RetailRocket']['apiUrl'] = 'http://api.retailrocket.ru/api/';
$c->partners['RetailRocket']['timeout'] = 0.5; //в секундах;
$c->partners['RetailRocket']['cookieLifetime'] = 2592000; // 30 дней
$c->partners['RetailRocket']['userEmail']['cookieName'] = 'user_email';

$c->partners['livetex']['enabled'] = false;
$c->partners['livetex']['liveTexID'] = 41836; // for enter.ru
//$c->partners['livetex']['liveTexID'] = 52705; // for olga.ent3.ru
$c->partners['AdLens']['enabled'] = true;
$c->partners['Сpaexchange']['enabled'] = true;
$c->partners['Admitad']['enabled'] = false;
$c->partners['Revolvermarketing']['enabled'] = true;
$c->partners['RuTarget']['enabled'] = true;
$c->partners['RuTarget']['containerId'] = 'GTM-4SJX';
$c->partners['Lamoda']['enabled'] = true;
$c->partners['Lamoda']['lamodaID'] = '11640775691088171491';
$c->partners['TagMan']['enabled'] = false;

// Myragon
$c->partners['Myragon']['enabled'] = true;
$c->partners['Myragon']['enterNumber'] = 1402;// номер Вашей кампании
$c->partners['Myragon']['secretWord'] = 'RdjJBC9FLE';// секретное слово
$c->partners['Myragon']['subdomainNumber'] = 49;// номер поддомена в сервисе Myragon

$c->adFox['enabled'] = true;

$c->partner['cookieName'] = 'last_partner';
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
$c->search['queryStringLimit'] = 1;
$c->search['categoriesLimit'] = 200;

$c->product['itemsPerPage']             = 20;
$c->product['showAccessories']          = true;
$c->product['showRelated']              = true;
$c->product['itemsInSlider']            = 5;
$c->product['itemsInCategorySlider']    = 3;
$c->product['itemsInAccessorySlider']   = 4;
$c->product['minCreditPrice']           = 3000;
$c->product['totalCount']               = 30000;
$c->product['recommendationSessionKey'] = 'recommendationProductIds';
// глобальный (без учета региона) список товаров
$c->product['globalListEnabled']      = true;
$c->product['showAveragePrice']       = false;
$c->product['allowBuyOnlyInshop']     = true;
$c->product['reviewEnabled']          = true;
$c->product['pushReview']             = true;
$c->product['lowerPriceNotification'] = true;
$c->product['furnitureConstructor']   = true;
// jewel
$c->product['itemsPerPageJewel']      = 24;
$c->product['itemsPerRowJewel']       = 4;
$c->product['pullRecommendation']     = true;
$c->product['pushRecommendation']     = true;

$c->productPhoto['url'] = [
    0 => '/1/1/60/',
    1 => '/1/1/120/',
    2 => '/1/1/163/',
    3 => '/1/1/500/',
    4 => '/1/1/2500/',
    5 => '/1/1/1500/',
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
    3 => '/6/1/500/',
    5 => '/6/1/960/'
];

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

$c->cart['productLimit'] = 30;
$c->cart['sessionName'] = 'userCart';

$c->payment['creditEnabled'] = true;
$c->payment['paypalECS'] = false;
$c->payment['blockedIds'] = [];

$c->smartengine['cert']           = $c->dataDir . '/cert/gsorganizationvalg2.crt';
$c->smartengine['apiUrl']         = 'https://www.selightprod.smartengine.at/se-light/api/1.0/json/';
$c->smartengine['apiKey']         = 'c41851b19511c20acc84f47b7816fb8e';
$c->smartengine['tenantid']       = 'ENojUTRcD8';
$c->smartengine['logEnabled']     = true;
$c->smartengine['logDataEnabled'] = true;
$c->smartengine['sslVerify']      = true;

$c->warranty['enabled'] = true;
$c->f1Certificate['enabled'] = true;
$c->coupon['enabled'] = true;
$c->blackcard['enabled'] = false;

$c->user['corporateRegister'] = true;
$c->user['defaultRoute'] = 'user.orders';

$c->database['host']     = 'site-db';
$c->database['name']     = 'enter';
$c->database['user']     = 'root';
$c->database['password'] = 'qazwsxedc';

$c->creditProvider['kupivkredit']['partnerId'] = '1-6ADAEAT';
$c->creditProvider['kupivkredit']['partnerName'] = 'ООО «Enter»';
$c->creditProvider['kupivkredit']['signature'] = 'enter-secret-7X5dwb92';

$c->queue['pidFile'] = (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid';
$c->queue['workerLimit'] = 10;
$c->queue['maxLockTime'] = 600;

$c->subscribe['enabled'] = true;
$c->subscribe['cookieName'] = 'subscribed';

$c->requestMainMenu = true;

$c->order['cookieName'] = 'last_order';
$c->order['sessionName'] = 'lastOrder';
$c->order['enableMetaTag'] = true;
$c->order['maxSumOnline'] = 15000;
$c->order['maxSumOnlinePaypal'] = 5000;
$c->order['excludedError'] = [705, 708, 735, 759, 800];
$c->order['addressAutocomplete'] = true;
// предоплата (SITE-2959)
$c->order['prepayment'] = [
    'enabled'    => true,
    'priceLimit' => 100000,// если стоимость заказа >= priceLimit, то появится плашка с текстом про предоплату
    'labelId'    => 15, // id шильдика "предоплата"
];

$c->newDeliveryCalc = true;

$c->kladr = [
    'token' => '52b04de731608f2773000000',
    'key' => 'c20b52a7dc6f6b28023e3d8ef81b9dbdb51ff74b',
    'itemLimit' => 1000,
];

$c->maybe3d['xmlUrl']     = 'http://hq.maybe3d.com/MappingService.svc/GetMappings?customerId=';
$c->maybe3d['customerId'] = 'BE2016EF-32D8-41E6-976F-A8D32EB20ACF';
$c->maybe3d['swfUrl']     = 'http://fs01.enter.ru/3d/flash/';
$c->maybe3d['cmsFolder']  = '/opt/wwwroot/cms.enter.ru/wwwroot/v1/video/product/';
$c->maybe3d['timeout']    = 30;

$c->img3d['cmsFolder']  = '/opt/wwwroot/cms.enter.ru/wwwroot/v1/video/product/';

$c->tag['numSidebarCategoriesShown'] = 3;

$c->sphinx['showFacets'] = false;
$c->sphinx['showListingSearchBar'] = false;

$c->lifeGift['enabled'] = false;
$c->lifeGift['regionId'] = 151021;
$c->lifeGift['labelId'] = 17;
$c->lifeGift['deliveryTypeId'] = 1077;

$c->enterprize['enabled'] = true;
$c->enterprize['formDataSessionKey'] = 'enterprizeForm';
$c->enterprize['itemsInSlider'] = 7;
$c->enterprize['showSlider'] = true;
$c->enterprize['cookieName'] = 'enterprize_coupon_sent';

$c->tchibo['rowWidth'] = 78;
$c->tchibo['rowHeight'] = 78;
$c->tchibo['rowPadding'] = 0;
$c->tchiboSlider['analytics'] = [
    'enabled' => true,
    'use_page_visibility' => true,
    'collection_view' => [
        'enabled' => true,
        'tchiboOnly' => true
    ],
    'collection_click' => [
        'enabled' => true,
        'tchiboOnly' => false
    ],
    'product_click' => [
        'enabled' => true,
        'tchiboOnly' => false
    ],
];

// настройки для АБ-тестов могут быть переопределены в json
$c->abtest['cookieName'] = 'switch';
$c->abtest['enabled']    = true;
$c->abtest['checkPeriod'] = 3600; //секунд - как часто проверять необходимость запуска теста
$c->abtest['bestBefore'] = '2014-09-04';
$c->abtest['test']       = [
    [
        'traffic'  => 33,
        'key'      => 'reviews_sprosikupi',
        'name'     => "Отзывы от sprosikupi",
        'ga_event' => 'reviews_sprosikupi',
    ],
    [
        'traffic'  => 33,
        'key'      => 'reviews_shoppilot',
        'name'     => "Отзывы от shoppilot",
        'ga_event' => 'reviews_shoppilot',
    ],
    [
        'traffic'  => 34,
        'key'      => 'reviews_default',
        'name'     => "Отзывы по умолчанию",
        'ga_event' => 'reviews_default',
    ],
];

$c->preview = false;

$c->svyaznoyClub['cookieLifetime'] = 2592000; // 30 дней
$c->svyaznoyClub['userTicket']['cookieName'] = 'UserTicket';
$c->svyaznoyClub['cardNumber']['cookieName'] = 'scid';

$c->flocktoryExchange['enabled'] = true;

$c->flocktoryCoupon['enabled'] = true;
$c->flocktoryCoupon['paramName'] = 'utm_coupon';


$c->photoContest = [
	'client' => [
		'url'          => 'http://photo.enter.ru/',
		'client_id'    => 'photocontest',
		'timeout'      => 2,
		'retryTimeout' => 1,
		'retryCount'   => 2,
		'debug'        => false,
	]
];

return $c;
