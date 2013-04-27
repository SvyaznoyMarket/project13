<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->coreV2['url'] = 'http://api.enter.ru/v2/';
$c->coreV2['timeout'] = null;
$c->coreV2['retryTimeout'] = [
    'default' => 0.5,
    'tiny'    => 0.05,
    'short'   => 0.2,
    'medium'  => 0.5,
    'long'    => 0.8,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->wordpress['url'] = 'http://content.enter.ru/';
$c->wordpress['timeout'] = 2;

$c->dataStore['url'] = 'http://cms.enter.ru/v1/';
$c->dataStore['timeout'] = 0.25;
$c->dataStore['retryTimeout'] = [
    'default' => 0.1,
    'tiny'    => 0.01,
    'short'   => 0.05,
    'medium'  => 0.1,
    'long'    => 0.2,
    'huge'    => 0.5,
    'forever' => 0,
];

$c->loadMediaHost = true;

$c->analytics['enabled'] = true;
$c->analytics['optimizelyEnabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;
$c->adFox['enabled'] = true;

$c->mainHost = 'www.enter.ru';
$c->mobileHost = 'm.enter.ru';

// промсвязьбанк
$c->paymentPsb['terminal'] = '20097201';
$c->paymentPsb['merchant'] = '000541120097201';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key'] = 'FD5AAE47574BB8EEFBF8C6C14CBCA453';
$c->paymentPsb['payUrl'] = 'https://3ds.payment.ru/cgi-bin/cgi_link';

// промсвязьбанк invoice
$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key'] = $c->dataDir . '/key/live.privkey.pem';
$c->paymentPsbInvoice['payUrl'] = 'https://retail.payment.ru/invoice.aspx';

$c->database['host']     = 'site-db'; // был 10.20.33.2

$c->smartengine['cert'] = $c->dataDir . '/cert/gsorganizationvalg2.crt';

$c->user['corporateRegister'] = true;

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

$c->f1Certificate['enabled'] = true;

$c->requestMainMenu = true;

$c->order['enableMetaTag'] = true;

return $c;
