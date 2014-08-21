<?php
/**
 * @var $page     \View\Order\CreatePage
 * @var $user     \Session\User
 * @var $provider \Payment\PsbInvoice\Provider
 * @var $order    \Model\Order\Entity
 * @var $form     \Payment\PsbInvoice\Form
 */

$backUrl = $page->url('order.paymentComplete', array('orderNumber' => $order->getNumber()), true);

if (\App::config()->newOrder && \App::abTest()->getTest('orders')) {
    if (\App::abTest()->getTest('orders')->getChosenCase()->getKey() == 'new') $backUrl = $page->url('order.complete', [], true);
}

if (!$form instanceof \Payment\PsbInvoice\Form) {
    $form = $provider->getForm($order, $backUrl);
} ?>

<form class="form jsPaymentForms jsPaymentFormPSBInvoice" method="post" action="<?= $provider->getPayUrl() ?>">
    <input type="hidden" name="ContractorID" value="<?= $form->getContractorId() ?>" />
    <input type="hidden" name="InvoiceID" value="<?= $form->getInvoiceId() ?>" />
    <input type="hidden" name="Sum" value="<?= $form->getSum() ?>" />
    <input type="hidden" name="PayDescription" value="<?= $form->getPayDescription() ?>" />
    <input type="hidden" name="AdditionalInfo" value="<?= $form->getAdditionalInfo() ?>" />
    <input type="hidden" name="Signature" value="<?= $form->getSignature() ?>" />

    <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
</form>