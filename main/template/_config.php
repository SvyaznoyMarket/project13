<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $config array
 */
?>

<?
$appConfig = \App::config();
$router = \App::router();

$config = array_merge([
    'jsonLog'               => $appConfig->jsonLog['enabled'],
    'userUrl'               => $router->generate('user.info'),
    'routeUrl'              => $router->generate('route'),
    'f1Certificate'         => $appConfig->f1Certificate['enabled'],
    'coupon'                => $appConfig->coupon['enabled'],
    'addressAutocomplete'   => $appConfig->order['addressAutocomplete'],
    'prepayment'            => $appConfig->order['prepayment'],
], isset($config) ? (array)$config : []);
?>

<div id="page-config" data-value="<?= $page->json($config) ?>"></div>
