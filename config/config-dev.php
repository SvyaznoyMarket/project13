<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

//$c->eventService['url'] = 'http://event.ent3.ru/';

$c->pickpoint['url'] = 'http://e-solution.pickpoint.ru/apitest/';

$c->loadMediaHost = false;

$c->jsonLog['enabled'] = false;
$c->analytics['enabled'] = false;
$c->googleAnalytics['enabled'] = false;
$c->yandexMetrika['enabled'] = false;
$c->adFox['enabled'] = false;
$c->googleTagManager['enabled'] = false;

$c->partners['Revolver']['enabled'] = false;
$c->partners['GetIntent']['enabled'] = false;
$c->partners['Сpaexchange']['enabled'] = false;
$c->partners['criteo']['enabled'] = false;
$c->partners['sociomantic']['enabled'] = false;
$c->partners['marin']['enabled'] = false;
$c->partners['alexa']['enabled'] = false;

$c->partners['AddThis']['enabled'] = false;
$c->partners['AdvMaker']['enabled'] = false;
$c->partners['Hubrus']['enabled'] = false;
$c->partners['SmartLeads']['enabled'] = false;
$c->partners['Sociaplus']['enabled'] = false;
$c->partners['ActionpayRetargeting']['enabled'] = false;
$c->partners['MyThings']['enabled'] = false;

$c->paymentPsb['terminal']     = '79036768';
$c->paymentPsb['merchant']     = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key']          = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl']       = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key']          = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl']       = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->mainMenu['requestMenu'] = false;
$c->mainMenu['recommendationsEnabled'] = false;

return $c;