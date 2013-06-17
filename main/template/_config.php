<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $config array
 */
?>

<?
$config = array_merge([
    'f1Certificate' => \App::config()->f1Certificate['enabled'],
    'coupon'        => \App::config()->coupon['enabled'],
], isset($config) ? (array)$config : []);
?>

<div id="site-config" data-value="<?= $page->json($config) ?>"></div>
