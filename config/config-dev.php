<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

//$c->coreV2['url'] = 'http://api.enter.ru/v2/';
//$c->coreV2['url'] = 'http://test2.core.ent3.ru/v2/';
$c->coreV2['url'] = 'http://core.ent3.ru/v2/';

$c->mobileDomain = 'm.ent3.ru';

$c->smartEngine['pull'] = true;
$c->smartEngine['push'] = false;

$c->warranty['enabled'] = true;

$c->paymentPsb['terminal'] = '79036768';
$c->paymentPsb['merchant'] = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key'] = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl'] = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key'] = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl'] = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->smartEngine['cert'] = $c->dataDir . '/cert/gsorganizationvalg2.crt';

return $c;