<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

$c->coreV2['url'] = 'http://api.enter.ru/v2/';

$c->wordpress['url'] = 'http://content.enter.ru/';

$c->analytics['enabled'] = true;
$c->googleAnalytics['enabled'] = true;
$c->yandexMetrika['enabled'] = true;

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
$c->paymentPsbInvoice['key'] = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl'] = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->database['host']     = '10.20.33.2';

$c->smartEngine['cert'] = $c->dataDir . '/cert/gsorganizationvalg2.crt';

return $c;