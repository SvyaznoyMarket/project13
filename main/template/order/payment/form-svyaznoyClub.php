<?php
/**
 * @deprecated
 * @var $page     \Templating\HtmlLayout
 * @var $user     \Session\User
 * @var $provider \Payment\SvyaznoyClub\Provider
 * @var $order    \Model\Order\Entity
 * @var $form     \Payment\SvyaznoyClub\Form|null
 */

if (!$form instanceof \Payment\SvyaznoyClub\Form) {
    $form = $provider->getForm($order);
} ?>

<form class="form" method="get" action="<?= $provider->getPayUrl() ?>">
    <input type="hidden" name="ShopId" value="<?= $form->getShopId() ?>" />
    <input type="hidden" name="OrderId" value="<?= $form->getOrderId() ?>" />
    <input type="hidden" name="MaxDiscount" value="<?= $form->getMaxDiscount() ?>" />
    <input type="hidden" name="TotalCost" value="<?= $form->getTotalCost() ?>" />
    <input type="hidden" name="UserTicket" value="<?= $form->getUserTicket() ?>" />
    <input type="hidden" name="Signature" value="<?= $form->getSignature() ?>" />

    <? if ($form->getCardNumber()): ?>
        <input type="hidden" name="CardNumber" value="<?= $form->getCardNumber() ?>" />
    <? elseif ($form->getEmail()): ?>
        <input type="hidden" name="Email" value="<?= $form->getEmail() ?>" />
    <? endif ?>

    <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
</form>