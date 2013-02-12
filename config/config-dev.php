<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

$c->coreV2['url'] = 'http://core.ent3.ru/v2/';

$c->coreV2['retryTimeout'] = [
    'default' => 1,
    'tiny'    => 0.2,
    'short'   => 0.4,
    'medium'  => 0.6,
    'long'    => 1,
    'huge'    => 1.5,
    'forever' => 0,
];

$c->mobileHost = 'm.ent3.ru';

$c->smartEngine['pull'] = true;
$c->smartEngine['push'] = false;
$c->smartEngine['api_url'] = 'https://selightstage.smartengine.at/se-light/api/1.0/json/';
$c->smartEngine['cert'] = $c->dataDir . '/cert/gsorganizationvalg2.crt';
$c->smartEngine['ssl_verify'] = false;

$c->paymentPsb['terminal'] = '79036768';
$c->paymentPsb['merchant'] = '790367686219999';
$c->paymentPsb['merchantName'] = 'Enter';
$c->paymentPsb['key'] = 'C50E41160302E0F5D6D59F1AA3925C45';
$c->paymentPsb['payUrl'] = 'http://193.200.10.117:8080/cgi-bin/cgi_link';

$c->paymentPsbInvoice['contractorId'] = 14;
$c->paymentPsbInvoice['key'] = $c->dataDir . '/key/privkey.pem';
$c->paymentPsbInvoice['payUrl'] = 'https://retail-tst.payment.ru/dn/Invoices/ReceiveUniversalInvoices.aspx';

$c->abtest['test'] = [
    [
        'traffic'  => '40',
        'key'      => 'comment',
        'name'     => 'Тестирование комментариев',
        'ga_event' => 'TestFreaks01',
    ],
    [
        'traffic'  => '40',
        'key'      => 'video',
        'name'     => 'Тестирование видео',
        'ga_event' => 'video',
    ],
];

return $c;