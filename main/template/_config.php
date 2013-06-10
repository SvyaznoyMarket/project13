<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<?
$config = [
    'f1Certificate' => \App::config()->f1Certificate['enabled'],
    'coupon'        => \App::config()->coupon['enabled'],
];
?>

<div id="site-config" data-value="<?= $page->json($config) ?>"></div>
