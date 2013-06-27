<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $config array
 */
?>

<?
$appConfig = \App::config();

$config = array_merge([
    'userUrl'       => \App::router()->generate('user.info'),
    'f1Certificate' => $appConfig->f1Certificate['enabled'],
    'coupon'        => $appConfig->coupon['enabled'],
], isset($config) ? (array)$config : []);
?>

<div id="page-config" data-value="<?= $page->json($config) ?>"></div>
