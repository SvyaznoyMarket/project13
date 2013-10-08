<?php
/**
 * @var $page          \View\Order\CreatePage
 * @var $user          \Session\User
 */

if ($link = \Partner\Counter\Actionpay::getSubscribeLink()):
    ?><img src="http://n.actionpay.ru/ok/4388.png?<?= $link ?>" height="1" width="1" /><?
endif;