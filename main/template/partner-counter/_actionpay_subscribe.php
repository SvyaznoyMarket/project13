<?php
/**
 * @var $user          \Session\User
 */

$link = \Partner\Counter\Actionpay::getSubscribeLink();
$params = $link ? $link : '';
if ($email = \App::request()->get('email')) {
    $params .= (!empty($link) ? '&' : '') . "email=$email";
}?>

<img src="http://apypxl.com/ok/4388.png?<?= $params ?>" height="1" width="1" />