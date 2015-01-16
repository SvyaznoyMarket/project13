<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

// $c->coreV2['url'] = 'http://tester.core.ent3.ru/v2/';

/*
$c->coreV2['timeout']      *= 1.5;
$c->corePrivate['timeout']      *= 1.5;

$c->reviewsStore['timeout']      *= 1.5;

$c->wordpress['timeout']        *= 1.5;
$c->wordpress['throwException'] = false;

$c->dataStore['timeout'] *= 1.5;
*/

$c->pickpoint['url'] = 'http://e-solution.pickpoint.ru/apitest/';

$c->database['host'] = 'localhost';

$c->loadMediaHost = false;

$c->jsonLog['enabled'] = false;
$c->analytics['enabled'] = false;
$c->analytics['optimizelyEnabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->adFox['enabled'] = false;

$c->paymentPsb['terminal']     = '79036768';
$c->paymentPsb['merchant']     = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key']          = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl']       = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key']          = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl']       = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->requestMainMenu = false;

$c->tealeaf['enabled'] = false;

return $c;