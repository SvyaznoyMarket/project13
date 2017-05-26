<?php

/** @var $c \Config\AppConfig */
$c = require __DIR__ . '/config.php';

$c->debug = true;

$c->mainHost = 'tester.ent3.ru';
$c->session['cookie_domain'] = '.ent3.ru';

$c->eventService['enabled'] = false;

/* CORE */
$c->core['url']         = 'http://tester.core.ent3.ru/';
$c->coreV2['url']         = 'http://tester.core.ent3.ru/v2/';
$c->corePrivate['url']    = 'http://tester.core.ent3.ru/private/';

/* SCMS */
$c->scms['url'] = 'http://scms.ent3.ru/';
$c->scmsV2['url'] = 'http://scms.ent3.ru/v2/';
$c->scmsSeo['url'] = 'http://scms.ent3.ru/seo/';
$c->reviewsStore['url']          = 'http://scms.ent3.ru/reviews/';

/* SPHINX */
$c->searchClient['url']          = 'http://search.enter.ru/';

return $c;