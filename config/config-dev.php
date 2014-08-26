<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

// $c->coreV2['url'] = 'http://tester.core.ent3.ru/v2/';

$c->coreV2['timeout']      *= 3;
$c->coreV2['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.6,
    'short'   => 1,
    'medium'  => 1.4,
    'long'    => 2,
    'huge'    => 3,
    'forever' => 0,
];
$c->coreV2['debug']        = false;

$c->corePrivate['timeout']      *= 3;
$c->corePrivate['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.2,
    'short'   => 0.4,
    'medium'  => 0.6,
    'long'    => 1,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->reviewsStore['timeout']      *= 5;
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

$c->wordpress['timeout']        *= 6;
$c->wordpress['throwException'] = false;

$c->dataStore['timeout'] *= 3;

$c->shopScript['timeout'] *= 3;

$c->pickpoint['url'] = 'http://e-solution.pickpoint.ru/apitest/';

$c->database['host'] = 'localhost';

$c->loadMediaHost = false;

$c->jsonLog['enabled'] = false;
$c->analytics['enabled'] = false;
$c->analytics['optimizelyEnabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->adFox['enabled'] = false;

$c->product['pullRecommendation'] = true;
$c->product['pushRecommendation'] = false;

$c->smartengine['apiUrl']    = 'https://selightstage.smartengine.at/se-light/api/1.0/json/';
$c->smartengine['cert']      = $c->dataDir . '/cert/gsorganizationvalg2.crt';
$c->smartengine['sslVerify'] = false;

$c->paymentPsb['terminal']     = '79036768';
$c->paymentPsb['merchant']     = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key']          = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl']       = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key']          = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl']       = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->requestMainMenu = false;

return $c;