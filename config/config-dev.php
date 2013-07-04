<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

$c->coreV2['url']          = 'http://tester.core.ent3.ru/v2/';
$c->coreV2['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.4,
    'short'   => 0.8,
    'medium'  => 1,
    'long'    => 1.5,
    'huge'    => 2,
    'forever' => 0,
];

$c->corePrivate['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.2,
    'short'   => 0.4,
    'medium'  => 0.6,
    'long'    => 1,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->reviewsStore['url']          = 'http://reviews.ent3.ru/reviews/';
$c->reviewsStore['timeout']      = 2;
$c->reviewsStore['retryCount']   = 3;
$c->reviewsStore['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.1,
    'short'   => 0.4,
    'medium'  => 1,
    'long'    => 1.6,
    'huge'    => 3,
    'forever' => 0,
];

$c->wordpress['throwException'] = false;
$c->wordpress['timeout'] = 2;

$c->dataStore['timeout'] = 2;

$c->dataStore['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.2,
    'short'   => 0.4,
    'medium'  => 0.6,
    'long'    => 1,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->database['host'] = 'localhost';

$c->mobileHost = 'm.ent3.ru';

$c->loadMediaHost = false;

$c->analytics['enabled'] = false;
$c->analytics['optimizelyEnabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->adFox['enabled'] = false;

$c->smartengine['pull']      = true;
$c->smartengine['push']      = false;
$c->smartengine['apiUrl']    = 'https://selightstage.smartengine.at/se-light/api/1.0/json/';
$c->smartengine['cert']      = $c->dataDir . '/cert/gsorganizationvalg2.crt';
$c->smartengine['sslVerify'] = false;

$c->crossss['id']      = 68; // *.ent3.ru
$c->crossss['apiKey']  = 'fe7fbe9540e14f1db1f9f047d1e54b25';
$c->crossss['timeout'] = 0.8;

$c->paymentPsb['terminal']     = '79036768';
$c->paymentPsb['merchant']     = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key']          = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl']       = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key']          = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl']       = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

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

$c->product['lowerPriceNotification'] = true;

return $c;