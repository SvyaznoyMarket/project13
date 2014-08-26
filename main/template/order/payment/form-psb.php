<?php
/**
 * @var $page     \View\Order\CreatePage
 * @var $user     \Session\User
 * @var $provider \Payment\Psb\Provider
 * @var $order    \Model\Order\Entity
 * @var $form     \Payment\Psb\Form|null
 */

$backUrl = $page->url('order.paymentComplete', array('orderNumber' => $order->getNumber()), true);

if (\App::config()->newOrder && \App::abTest()->getTest('orders')) {
    if (\App::abTest()->getTest('orders')->getChosenCase()->getKey() == 'new') $backUrl = $page->url('order.complete', [], true);
}

if (!$form instanceof \Payment\Psb\Form) {
    $form = $provider->getForm($order, $backUrl);
} ?>

<form class="form jsPaymentForms jsPaymentFormPSB" method="post" action="<?= $provider->getPayUrl() ?>">
    <input type="hidden" name="AMOUNT" value="<?= $form->getAmount() ?>" />
    <input type="hidden" name="CURRENCY" value="<?= $form->getCurrency() ?>" />
    <input type="hidden" name="ORDER" value="<?= $form->getOrder() ?>" />
    <input type="hidden" name="DESC" value="<?= $form->getDesc() ?>" />
    <input type="hidden" name="TERMINAL" value="<?= $form->getTerminal() ?>" />
    <input type="hidden" name="TRTYPE" value="<?= $form->getTrtype() ?>" />
    <input type="hidden" name="MERCH_NAME" value="<?= $form->getMerchName() ?>" />
    <input type="hidden" name="MERCHANT" value="<?= $form->getMerchant() ?>" />
    <input type="hidden" name="EMAIL" value="<?= $form->getEmail() ?>" />
    <input type="hidden" name="TIMESTAMP" value="<?= $form->getTimestamp() ?>" />
    <input type="hidden" name="NONCE" value="<?= $form->getNonce() ?>" />
    <input type="hidden" name="BACKREF" value="<?= $form->getBackref() ?>" />
    <input type="hidden" name="P_SIGN" value="<?= $form->getPSign() ?>" />

    <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
</form>