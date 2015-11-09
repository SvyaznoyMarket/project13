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
$c->logDir = realpath($c->appDir . (basename($c->appDir) !== 'wwwroot' ? '/..' : '') . '/../logs');
$c->webDir = $c->appDir . '/web';
$c->templateDir = $c->appDir . '/lite/template';

// project Lite
$c->lite['enabled'] = true;
$c->feedback['enabled'] = true;
$c->feedback['email'] = 'feedback@wallashop.com';

$c->controllerPrefix = 'Controller';
$c->routePrefix = '';

$c->debug = false;
$c->logger['pretty'] = false;
$c->appName = 'Enter';

$c->authToken['name']     = '_token';
$c->authToken['authorized_cookie'] = '_authorized';
$c->authToken['disposableTokenParam'] = 'authToken'; // имя параметра для одноразовой аутенфикации

$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 2592000; // 30 дней
$c->session['cookie_domain'] = array_key_exists('HTTP_HOST', $_SERVER) ? '.' . implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), -2)) : '.sordex.ru';
#$c->session['cookie_domain'] = '.wallashop.com';
$c->session['compareKey']   = 'compare'; // ключ для массива сравнения
$c->session['favouriteKey'] = 'favourite'; // ключ для масссива избранного

$c->mainHost = array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : 'www.sordex.ru';
$c->mobileHost = array_key_exists('HTTP_HOST', $_SERVER) ? 'm.' . implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), -2)) : 'm.sordex.ru';
$c->description = 'Enter – это все товары для жизни по интернет-ценам. В Enter вы можете купить что угодно, когда угодно и любым удобным для Вас способом!';

$c->redirect301['enabled'] = true;

$c->curlCache['enabled'] = true;
$c->curlCache['delayRatio'] = [0, 0.025]; // количество и время задержек

$c->coreV2['url']          = 'http://api.enter.ru/v2/';
$c->coreV2['client_id']    = 'site';
$c->coreV2['timeout']      = 4 * 2;
$c->coreV2['hugeTimeout']  = 90;
$c->coreV2['retryCount']   = 2;
$c->coreV2['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
];
$c->coreV2['chunk_size']   = 30;
$c->coreV2['debug']        = false;

$c->eventService['url'] = 'http://event.enter.ru/';
$c->eventService['enabled'] = false; // FIXME
$c->eventService['timeout'] = 0.2 * 2;
$c->eventService['client_id'] = 'site';

$c->corePrivate['url']          = 'http://api.enter.ru/private/';
$c->corePrivate['user']         = 'Developer';
$c->corePrivate['password']     = 'dEl23sTOas';
$c->corePrivate['timeout']      = 4 * 2;
$c->corePrivate['retryCount']   = 2;
$c->corePrivate['retryTimeout'] = [
    'default' => 1.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
];

$c->searchClient['url']          = 'http://search.enter.ru/';
$c->searchClient['client_id']    = 'site';
$c->searchClient['timeout']      = 4 * 2;
$c->searchClient['hugeTimeout']  = 90;
$c->searchClient['retryCount']   = 2;
$c->searchClient['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
];
$c->searchClient['chunk_size']   = 50;
$c->searchClient['debug']        = false;

$c->oauthEnabled['vkontakte'] = true;
$c->oauthEnabled['facebook'] = true;

$c->vkontakteOauth->clientId     = '4514389';
$c->vkontakteOauth->secretKey    = 'AtltsKfjxrvrJsBNbqgV';

$c->facebookOauth->clientId     = '699159936838172';
$c->facebookOauth->secretKey    = '98f2019667addd352ae41e4b803ff4c2';

$c->odnoklassnikiOauth->clientId     = '1099656960';
$c->odnoklassnikiOauth->secretKey    = '03F94E54F81231EFCDE26D57';
$c->odnoklassnikiOauth->publicKey    = 'CBAQGMICEBABABABA';

//$c->twitterOauth->clientId     = 'tCBHggGaHaFAUNxzCXIv07f9w';
//$c->twitterOauth->secretKey    = 'UStteR1Kyt81ur9WF20OMOjUdzp4GhwrFDuBmidTwyNsEZbEpH';


$c->reviewsStore['url']          = 'http://scms.enter.ru/reviews/';
$c->reviewsStore['retryCount']   = 2;
$c->reviewsStore['timeout']      = 0.6 * 4;
$c->reviewsStore['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
];

$c->dataStore['url'] = 'http://cms.enter.ru/v1/';
$c->dataStore['timeout'] = 2 * 2;
$c->dataStore['retryCount'] = 2;
$c->dataStore['retryTimeout'] = [
    'default' => 0.04,
    'tiny'    => 0.04,
    'short'   => 0.08,
    'medium'  => 0.1,
    'long'    => 0.5,
    'huge'    => 1,
];

$c->scms['url']          = 'http://scms.enter.ru/';
$c->scms['retryCount']   = 2;
$c->scms['timeout']      = 3 * 2;
$c->scms['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
];

$c->scmsV2['url']          = 'http://scms.enter.ru/v2/';
$c->scmsV2['retryCount']   = 2;
$c->scmsV2['timeout']      = 3 * 2;
$c->scmsV2['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
];

$c->scmsSeo['url']          = 'http://scms.enter.ru/seo/';
$c->scmsSeo['retryCount']   = 2;
$c->scmsSeo['timeout']      = 3 * 2;
$c->scmsSeo['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
];

$c->crm['url'] = 'http://crm.enter.ru/';
$c->crm['client_id'] = 'site';
$c->crm['timeout'] = 3 * 2;
$c->crm['hugeTimeout'] = 4;
$c->crm['retryCount'] = 2;
$c->crm['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.1,
    'short'   => 0.3,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
];
$c->crm['debug'] = false;

$c->fileStorage = [
    'url'          => 'http://api.enter.ru/v2/',
    'client_id'    => 'site',
    'timeout'      => 5 * 2,
    'retryTimeout' => [
        'default' => 0.18,
        'tiny'    => 0.18,
        'short'   => 0.25,
        'medium'  => 0.5,
        'long'    => 1,
        'huge'    => 2,
    ],
    'retryCount'   => 2,
];

$c->connectTerminal = true;

$c->company['phone'] = '+7 (495) 135-10-70';
//$c->company['moscowPhone'] = '+7 (495) 775-00-06';
//$c->company['spbPhone'] = '+7 (812) 703-77-30';
//$c->company['icq'] = '648198963';

$c->jsonLog['enabled'] = true;
$c->analytics['enabled'] = true;
$c->googleAnalytics['enabled'] = false;
$c->googleAnalytics['secondary.enabled'] = false;
$c->googleAnalyticsTchibo['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->googleTagManager['enabled'] = false;
$c->googleTagManager['containerId'] = 'GTM-P65PBR';

$c->pickpoint['url'] = 'http://e-solution.pickpoint.ru/api/';
$c->pickpoint['timeout'] = 60;
$c->pickpoint['retryCount'] = 2;
$c->pickpoint['retryTimeout'] = [
    'default' => 0.04,
    'tiny'    => 0.04,
    'short'   => 0.08,
    'medium'  => 0.1,
    'long'    => 0.5,
    'huge'    => 1,
];

// TODO: Вынести сюда же настройки для get4click
$c->partners['criteo']['enabled'] = true;
$c->partners['criteo']['account'] = 10442;

$c->partners['sociomantic']['enabled'] = true;
$c->partners['marin']['enabled'] = true;
$c->partners['alexa']['enabled'] = true;

// RetailRocket
$c->partners['RetailRocket']['account'] = '519c7f3c0d422d0fe0ee9775';
$c->partners['RetailRocket']['apiUrl'] = 'http://api.retailrocket.ru/api/';
$c->partners['RetailRocket']['timeout'] = 0.33 * 4; //в секундах;
$c->partners['RetailRocket']['cookieLifetime'] = 2592000; // 30 дней
$c->partners['RetailRocket']['userEmail']['cookieName'] = 'user_email';

$c->partners['livetex']['enabled'] = true;
$c->partners['livetex']['liveTexID'] = 41836; // for enter.ru
$c->partners['MyThings']['enabled'] = true;
$c->partners['Сpaexchange']['enabled'] = true;
$c->partners['TagMan']['enabled'] = false;
$c->partners['Revolver']['enabled'] = true;
$c->partners['Insider']['enabled'] = true;
$c->partners['GetIntent']['enabled'] = true;
$c->partners['AddThis']['enabled'] = true;
$c->partners['AdvMaker']['enabled'] = true;
$c->partners['Hubrus']['enabled'] = true;
$c->partners['CityAdsRetargeting']['enabled'] = true;
$c->partners['Sociaplus']['enabled'] = true;
$c->partners['ActionpayRetargeting']['enabled'] = true;
$c->partners['MnogoRu']['enabled'] = true;
$c->partners['MnogoRu']['cookieName'] = 'enter_mnogo_ru';
$c->partners['PandaPay']['cookieName'] = 'enter_panda_pay';
$c->partners['LinkProfit']['enabled'] = true;
$c->partners['LinkProfit']['cookieName'] = 'linkprofit_id';
$c->partners['Adblender']['enabled'] = true;

$c->partners['Giftery']['enabled'] = true;
$c->partners['Giftery']['lowestPrice'] = 500;

// SITE-6208
$c->partners['soloway']['enabled'] = true;
$c->partners['soloway']['id'] = 209723;

$c->partners['facebook']['enabled'] = true;

$c->adFox['enabled'] = true;

$c->partner['cookieName'] = 'last_partner';
$c->partner['secondClickCookieName'] = 'last_partner_sec_click'; // SITE-4834
$c->partner['cookieLifetime'] = 2592000; // 30 дней

$c->onlineCall['enabled'] = true;

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
$c->region['cache']          = false;

$c->shop['cookieName'] = 'shopid';
$c->shop['cookieLifetime'] = 31536000; // 365 дней
$c->shop['autoresolve']    = true;
$c->shop['enabled'] = true;

$c->search['itemLimit'] = 1000;
$c->search['queryStringLimit'] = 1;
$c->search['categoriesLimit'] = 200;

$c->product['itemsPerPage']             = 20;
$c->product['showAccessories']          = true;
$c->product['showRelated']              = true;
$c->product['getModel']                 = true;
$c->product['deliveryCalc']             = true;
$c->product['itemsInSlider']            = 5;
$c->product['itemsInCategorySlider']    = 3;
$c->product['itemsInAccessorySlider']   = 4;
$c->product['totalCount']               = 55000;
$c->product['recommendationSessionKey']     = 'recommendationProductIds';
$c->product['recommendationProductLimit']   = 30;
$c->product['productPageSendersSessionKey'] = 'productPageSenders';
$c->product['productPageSenders2SessionKey'] = 'productPageSendersForMarketplace';
$c->product['showAveragePrice']       = false;
$c->product['allowBuyOnlyInshop']     = true;
$c->product['reviewEnabled']          = true;
$c->product['creditEnabledInCard']    = true;
$c->product['couponEnabledInCard']    = true;
$c->product['pushReview']             = true;
$c->product['lowerPriceNotification'] = true;
// jewel
$c->product['itemsPerPageJewel']      = 24;
$c->product['itemsPerRowJewel']       = 4;
$c->product['pullRecommendation']     = false;
$c->product['pushRecommendation']     = true;
$c->product['viewedEnabled']          = true;

$c->banner['timeout'] = 5000;
$c->banner['checkStatus'] = true;

$c->cart['productLimit'] = 0;
$c->cart['sessionName'] = 'cart';
$c->cart['checkStock'] = false;
$c->cart['updateTime'] = 1; // обновлять корзину, если данные в ней устарели более, чем на 1 минуту

$c->payment['creditEnabled'] = true;
$c->payment['blockedIds'] = [];

$c->user['enabled'] = false;
$c->user['corporateRegister'] = true;
$c->user['defaultRoute'] = 'user.recommend';

$c->database['host']     = 'site-db';
$c->database['name']     = 'enter';
$c->database['user']     = 'root';
$c->database['password'] = 'qazwsxedc';

$c->creditProvider['kupivkredit']['partnerId'] = '1-6ADAEAT';
$c->creditProvider['kupivkredit']['partnerName'] = 'ООО «Enter»';
$c->creditProvider['kupivkredit']['signature'] = 'enter-secret-7X5dwb92';
$c->creditProvider['directcredit']['partnerId'] = '4427';

$c->queue['pidFile'] = (sys_get_temp_dir() ?: '/tmp').'/enter-queue.pid';
$c->queue['workerLimit'] = 10;
$c->queue['maxLockTime'] = 600;

$c->subscribe['enabled'] = true;
$c->subscribe['getChannel'] = true;
$c->subscribe['cookieName'] = 'subscribed';
$c->subscribe['cookieName2'] = 'enter_subscribed_ch';   // кука вида {channelId:status}
$c->subscribe['cookieName3'] = 'enter_wanna_subscribe'; // кука о желании подписки в новом ОЗ

$c->mainMenu['recommendationsEnabled'] = false;

$c->newOrder = true;
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
    'labelId'    => 15, // id шильдика "предоплата"
];
$c->order['splitSessionKey'] = 'order_split';
$c->order['oneClickSplitSessionKey'] = $c->order['splitSessionKey'] . '-1click';
$c->order['sessionInfoOnComplete'] = true; // краткая инфа о заказе
$c->order['emailRequired'] = true; // обязательность email
$c->order['creditStatusSessionKey'] = 'order_credit';
$c->order['channelSessionKey'] = 'order_channel';

$c->newDeliveryCalc = true;

$c->kladr = [
    'token'     => '52b04de731608f2773000000',
    'key'       => 'c20b52a7dc6f6b28023e3d8ef81b9dbdb51ff74b',
    'itemLimit' => 20,
];

$c->sphinx['showFacets'] = false;
$c->sphinx['showListingSearchBar'] = false;

$c->lifeGift['enabled'] = false;
$c->lifeGift['regionId'] = 151021;
$c->lifeGift['labelId'] = 17;
$c->lifeGift['deliveryTypeId'] = 1077;

$c->enterprize['enabled'] = false;
$c->enterprize['formDataSessionKey'] = 'enterprizeForm';
$c->enterprize['itemsInSlider'] = 7;
$c->enterprize['showSlider'] = true;

$c->tchibo['rowWidth'] = 78;
$c->tchibo['rowHeight'] = 78;
$c->tchibo['rowPadding'] = 0;
$c->tchiboSlider['analytics'] = [
    'enabled' => true,
    'use_page_visibility' => true
];

$c->abTest = [
    'enabled'    => true,
    'cookieName' => 'switch',
    'tests'      => [],
];

$c->self_delivery['enabled'] = true;
$c->self_delivery['limit'] = 500;
$c->self_delivery['regions'] = [119623, 93746, 14974];

$c->minOrderSum = 990;

$c->preview = false;

$c->svyaznoyClub['cookieLifetime'] = 2592000; // 30 дней
$c->svyaznoyClub['userTicket']['cookieName'] = 'UserTicket';
$c->svyaznoyClub['cardNumber']['cookieName'] = 'scid';

$c->flocktory['site_id'] = '427';
$c->flocktory['exchange'] = false;
$c->flocktory['postcheckout'] = false;
$c->flocktory['precheckout'] = false;

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

$c->rootCategoryUi = '00000000-0000-0000-0000-000000000000';

$c->siteVersionSwitcher['cookieName'] = 'mobile';
$c->siteVersionSwitcher['cookieLifetime'] = 20 * 365 * 24 * 60 * 60;

$c->bandit['enabled'] = false;

return $c;
