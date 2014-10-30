<?php
/**
 * @var $page     \View\Order\CreatePage
 * @var $user     \Session\User
 * @var $provider \Payment\PsbInvoice\Provider
 * @var $order    \Model\Order\Entity
 * @var $form     \Payment\PsbInvoice\Form
 */

$backUrl = $page->url('order.paymentComplete', array('orderNumber' => $order->getNumber()), true);

$region = \App::user()->getRegion();
if ($region && \App::config()->newOrder) {
    $ordersNewTest = \App::abTest()->getTest('orders_new');
    $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
    if (
        (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для остальных регионов
        || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
    ) {
        $backUrl = $page->url('order.complete', [], true);
    }
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