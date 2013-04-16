<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<?
$config = \App::config();
$attributes = [];

if ($config->f1Certificate['enabled']) {
    $attributes[] = 'data-f1-certificate="true"';
}
?>

<div id="site-config" <?= implode(' ', $attributes) ?>></div>