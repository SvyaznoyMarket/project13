<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = false;

//$c->coreV2['url']        = 'http://tester.core.ent3.ru/v2/';
//$c->coreV2['url']        = 'http://cr1.core.ent3.ru/v2/';

//$c->corePrivate['url']   = 'http://tester.core.ent3.ru/private/';

//$c->reviewsStore['url']  = 'http://scms.ent3.ru/reviews/';
//$c->scms['url']          = 'http://scms.ent3.ru/';
//$c->scmsV2['url']        = 'http://scms.ent3.ru/v2/';
//$c->scmsSeo['url']       = 'http://scms.ent3.ru/seo/';

//$c->searchClient['url']  = 'http://search.ent3.ru/';

//$c->coreV2['timeout']       = 30;
//$c->corePrivate['timeout']  = 30;
//$c->reviewsStore['timeout'] = 30;
//$c->scms['timeout']         = 30;
//$c->scmsV2['timeout']       = 30;
//$c->scmsSeo['timeout']      = 30;
//$c->searchClient['timeout'] = 30;

$c->eventService['enabled'] = true;

return $c;
