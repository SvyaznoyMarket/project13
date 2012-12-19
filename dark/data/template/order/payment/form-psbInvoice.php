<?php
/**
 * @var $page     \View\Order\CreatePage
 * @var $user     \Session\User
 * @var $provider \Payment\PsbInvoice\Provider
 * @var $order    \Model\Order\Entity
 */
?>

<? $form = $provider->getForm($order, $page->url('order.payment', array('orderNumber' => $order->getNumber()), true)) ?>

<form class="form" method="post" action="<?= $provider->getPayUrl() ?>">
    <input type="hidden" name="ContractorID" value="<?= $form->getContractorId() ?>" />
    <input type="hidden" name="InvoiceID" value="<?= $form->getInvoiceId() ?>" />
    <input type="hidden" name="Sum" value="<?= $form->getSum() ?>" />
    <input type="hidden" name="PayDescription" value="<?= $form->getPayDescription() ?>" />
    <input type="hidden" name="AdditionalInfo" value="<?= $form->getAdditionalInfo() ?>" />
    <input type="hidden" name="Signature" value="<?= $form->getSignature() ?>" />

    <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
</form>