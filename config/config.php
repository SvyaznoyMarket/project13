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
$c->templateDir = $c->appDir . '/main/template';

$c->secretKey = 'dhG8N1beBcTe';

$c->controllerPrefix = 'Controller';
$c->routeUrlPrefix = '';

$c->debug = false;
$c->logger['pretty'] = false;
$c->logger['emptyChance'] = 0;
$c->appName = 'Enter';

$c->session['name']            = 'enter';
$c->session['cookie_lifetime'] = 2592000; // 30 дней
$c->session['cookie_domain'] = '.enter.ru';
$c->session['compareKey']   = 'compare'; // ключ для массива сравнения
$c->session['favouriteKey'] = 'favourite'; // ключ для масссива избранного

$c->mainHost = 'www.enter.ru';
$c->mobileHost = 'm.enter.ru';
$c->description = 'Enter – это все товары для жизни по интернет-ценам. В Enter вы можете купить что угодно, когда угодно и любым удобным для Вас способом!';

$c->redirect301['enabled'] = true;

$c->curlCache['enabled'] = true;
$c->curlCache['delayRatio'] = [0, 0.025]; // количество и время задержек

$c->core['url']          = 'http://api.enter.ru/';
$c->core['client_id']    = 'site';
$c->core['timeout']      = 4;
$c->core['hugeTimeout']  = 90;
$c->core['retryCount']   = 2;
$c->core['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
];
$c->core['chunk_size']   = 30;
$c->core['debug']        = false;

$c->coreV2['url']          = 'http://api.enter.ru/v2/';
$c->coreV2['client_id']    = 'site';
$c->coreV2['timeout']      = 4;
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
$c->eventService['enabled'] = true; // FIXME
$c->eventService['timeout'] = 0.2;
$c->eventService['client_id'] = 'site';

$c->corePrivate['url']          = 'http://api.enter.ru/private/';
$c->corePrivate['user']         = 'Developer';
$c->corePrivate['password']     = 'dEl23sTOas';
$c->corePrivate['timeout']      = 4;
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
$c->searchClient['timeout']      = 4;
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
$c->reviewsStore['timeout']      = 0.6;
$c->reviewsStore['retryTimeout'] = [
    'default' => 0.18,
    'tiny'    => 0.18,
    'short'   => 0.25,
    'medium'  => 0.5,
    'long'    => 1,
    'huge'    => 2,
];

$c->scms['url']          = 'http://scms.enter.ru/';
$c->scms['retryCount']   = 2;
$c->scms['timeout']      = 3;
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
$c->scmsV2['timeout']      = 3;
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
$c->scmsSeo['timeout']      = 3;
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
$c->crm['timeout'] = 1.5;
$c->crm['hugeTimeout'] = 2.5;
$c->crm['retryCount'] = 1;
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
    'timeout'      => 5,
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

$c->company['phone'] = '+7 (800) 775-52-92';
$c->company['moscowPhone'] = '+7 (495) 108-07-73';

$c->analytics['enabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->googleAnalytics['secondary.enabled'] = false;
$c->googleAnalyticsTchibo['enabled'] = true;
$c->yandexMetrika['enabled'] = true;
$c->googleTagManager['enabled'] = true;
$c->googleTagManager['containerId'] = 'GTM-P65PBR';

// RetailRocket
$c->partners['RetailRocket']['account'] = '519c7f3c0d422d0fe0ee9775';
$c->partners['RetailRocket']['apiUrl'] = 'http://api.retailrocket.ru/api/';
$c->partners['RetailRocket']['timeout'] = 0.33; //в секундах;
$c->partners['RetailRocket']['cookieLifetime'] = 2592000; // 30 дней
$c->partners['RetailRocket']['userEmail']['cookieName'] = 'user_email';

// отключен, т.к. с 2016 года не используется (оплата услуг livetex'а была прекращена) и загружает 2 картинки по http://
// (что неприемлемо после перехода сайта на https://)
$c->partners['livetex']['enabled'] = false;
$c->partners['livetex']['liveTexID'] = 41836; // for enter.ru
$c->partners['GetIntent']['enabled'] = false;
$c->partners['AddThis']['enabled'] = true;
$c->partners['CityAdsRetargeting']['enabled'] = false;
$c->partners['ActionpayRetargeting']['enabled'] = true;
$c->partners['MnogoRu']['enabled'] = false;
$c->partners['MnogoRu']['cookieName'] = 'enter_mnogo_ru';
$c->partners['sberbankSpasibo']['enabled'] = false;

$c->partners['Giftery']['enabled'] = false;
$c->partners['Giftery']['lowestPrice'] = 500;

// SITE-6208
$c->partners['soloway']['enabled'] = false;
$c->partners['soloway']['id'] = 209723;

$c->partners['admitad']['enabled'] = true;

$c->partners['facebook']['enabled'] = true;

$c->adFox['enabled'] = true;

$c->partner['cookieName'] = 'last_partner';
$c->partner['cookieLifetime'] = 2592000; // 30 дней

$c->onlineCall['enabled'] = false;

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
$c->product['getModelInListing']        = true;
$c->product['getModelInCard']           = true;
$c->product['deliveryCalc']             = true;
$c->product['showDeliveryPrice']        = true;
$c->product['smartChoiceEnabled']       = true;
$c->product['breadcrumbsEnabled']       = true;
$c->product['itemsInSlider']            = 5;
$c->product['itemsInCategorySlider']    = 3;
$c->product['itemsInAccessorySlider']   = 4;
$c->product['totalCount']               = 55000;
$c->product['recommendationSessionKey']     = 'recommendationProductIds';
$c->product['recommendationProductLimit']   = 30;
$c->product['allowBuyOnlyInshop']     = true;
$c->product['reviewEnabled']          = true;
$c->product['creditEnabledInCard']    = true;
$c->product['pushReview']             = true;
$c->product['lowerPriceNotification'] = true;
$c->product['pullRecommendation']     = true;
$c->product['pullMainRecommendation'] = true;
$c->product['pushRecommendation']     = true;
$c->product['viewedEnabled']          = true;

$c->banner['timeout'] = 5000;
$c->banner['checkStatus'] = true;

$c->cart['productLimit'] = 0;
$c->cart['sessionName'] = 'cart';
$c->cart['checkStock'] = false;
$c->cart['updateTime'] = 1; // обновлять корзину, если данные в ней устарели более, чем на 1 минуту
$c->cart['oneClickOnly'] = false; // важно! только при деградации true

$c->payment['creditEnabled'] = false;
$c->payment['blockedIds'] = [];

$c->authToken['name']     = '_token';
$c->authToken['authorized_cookie'] = '_authorized';
$c->authToken['disposableTokenParam'] = 'authToken'; // имя параметра для одноразовой аутенфикации

$c->user['tokenSessionKey']   = 'userToken';
$c->user['corporateRegister'] = true;
$c->user['defaultRoute'] = 'user';
$c->user['infoCookieName'] = 'user_info';

$c->creditProvider['kupivkredit']['partnerId'] = '1-6ADAEAT';
$c->creditProvider['kupivkredit']['partnerName'] = 'ООО «Enter»';
$c->creditProvider['kupivkredit']['signature'] = 'enter-secret-7X5dwb92';
$c->creditProvider['directcredit']['partnerId'] = '4427';

$c->subscribe['enabled'] = true;
$c->subscribe['getChannel'] = true;
$c->subscribe['cookieName'] = 'subscribed';
$c->subscribe['cookieName2'] = 'enter_subscribed_ch';   // кука вида {channelId:status}
$c->subscribe['cookieName3'] = 'enter_wanna_subscribe'; // кука о желании подписки в новом ОЗ

$c->mainMenu['recommendationsEnabled'] = true;
$c->mainMenu['maxLevel'] = 3;

$c->order['cookieName'] = 'last_order';
$c->order['sessionName'] = 'lastOrder';
$c->order['enableMetaTag'] = true;
$c->order['maxSumOnline'] = 15000;
$c->order['maxSumOnlinePaypal'] = 5000;
$c->order['excludedError'] = [705, 708, 735, 759, 800];
$c->order['addressAutocomplete'] = true;
// предоплата (SITE-2959)
$c->order['prepayment']['enabled'] = true;
$c->order['prepayment']['labelId'] = 15; // id шильдика "предоплата"
$c->order['splitSessionKey'] = 'order_split';
$c->order['splitUndoSessionKey'] = 'order_split_undo';
$c->order['splitAddressAdditionSessionKey'] = 'order_split_address_addition';
$c->order['oneClickSplitSessionKey'] = $c->order['splitSessionKey'] . '-1click';
$c->order['emailRequired'] = true; // обязательность email
$c->order['creditStatusSessionKey'] = 'order_credit';
$c->order['channelSessionKey'] = 'order_channel';
$c->order['checkCertificate'] = false;
$c->order['enableDiscountCodes'] = false;

$c->kladr = [
    'token'     => '52b04de731608f2773000000',
    'key'       => 'c20b52a7dc6f6b28023e3d8ef81b9dbdb51ff74b',
    'itemLimit' => 20,
];

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

$c->minOrderSum = 990;

$c->preview = false;

$c->svyaznoyClub['cookieLifetime'] = 2592000; // 30 дней
$c->svyaznoyClub['userTicket']['cookieName'] = 'UserTicket';
$c->svyaznoyClub['cardNumber']['cookieName'] = 'scid';

$c->flocktory['site_id'] = '427';
$c->flocktory['postcheckout'] = true;
$c->flocktory['precheckout'] = true;

$c->rootCategoryUi = '00000000-0000-0000-0000-000000000000';

$c->siteVersionSwitcher['cookieName'] = 'mobile';
$c->siteVersionSwitcher['cookieLifetime'] = 20 * 365 * 24 * 60 * 60;

$c->richRelevance['enabled'] = true;
$c->richRelevance['timeout'] = .3;
$c->richRelevance['apiKey'] = '951a5607dc020e11';
$c->richRelevance['apiClientKey'] = '911c9da5198d9f42';
$c->richRelevance['apiUrl']    = 'http://recs.richrelevance.com/rrserver/api/rrPlatform/';
$c->richRelevance['jsUrl']    = '//media.richrelevance.com/rrserver/js/1.1/p13n.js';

$c->useNodeMQ = false;
$c->nodeMQ = [
	'host'	=> 'api.enter.ru',
	'port'		=> '8888'
];

$c->userCallback['enabled'] = false;
$c->userCallback['timeFrom'] = 28800;
$c->userCallback['timeTo'] = 79200;

return $c;
