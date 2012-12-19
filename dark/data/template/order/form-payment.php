<?php
/**
 * @var $page     \View\Order\CreatePage
 * @var $user     \Session\User
 * @var $provider \Payment\ProviderInterface
 * @var $order    \Model\Order\Entity
 */
?>

<? if ($provider instanceof \Payment\Psb\Provider): ?>
    <?= $page->render('order/payment/form-psb', array('provider' => $provider, 'order' => $order)) ?>
<? elseif ($provider instanceof \Payment\PsbInvoice\Provider): ?>
    <?= $page->render('order/payment/form-psbInvoice', array('provider' => $provider, 'order' => $order)) ?>
<? endif ?>