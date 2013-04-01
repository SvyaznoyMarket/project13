<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 * @var $servicesById       \Model\Product\Service\Entity[]
 * @var $shopsById          \Model\Shop\Entity[]
 * @var $isCredit           bool
 * @var $paymentProvider    \Payment\ProviderInterface
 * @var $creditData         array
 * @var $isOrderAnalytics   bool
 */
?>

<?php
$isCorporative = $user->getEntity() ? $user->getEntity()->getIsCorporative() : false;
// TODO: доделать
$isCredit = (bool)$creditData;
$isOrderAnalytics = isset($isOrderAnalytics) ? $isOrderAnalytics : true;
?>

<!-- Header -->
<div class='bBuyingHead'>
    <a href="<?= $page->url('homepage') ?>"></a>
    <i>Оформление заказа</i><br>
    <span><?= $isCredit ? 'Покупка в кредит' : 'Ваш заказ принят, спасибо!' ?></span>
</div>
<!-- /Header -->

<? foreach ($orders as $order): ?>
    <p class="font19">Номер заказа: <?= $order->getNumber() ?></p>

    <? if ($order->getDeliveredAt() instanceof \DateTime): ?>
        <p class="font16">Дата доставки: <?= $order->getDeliveredAt()->format('d.m.Y') ?></p>
    <? endif ?>

    <p class="font16">Сумма заказа: <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></p>
    <p class="font16">Сумма для оплаты: <span id="paymentWithCard"><?= $page->helper->formatPrice($order->getPaySum()) ?></span> <span class="rubl">p</span></p>
    <div class="line pb15"></div>
<?php endforeach ?>

<? if ($isCorporative): ?>
    <div class="mt32">
        В ближайшее время мы оповестим вас о выставлении счета в <strong><a href="<?= $page->url('user.order') ?>">личном кабинете</a></strong>.
    </div>
<? endif ?>

<? if (!$isCredit && !$isCorporative): ?>
<div class="mt32">
    Спасибо за заказ! Сейчас он отправлен на склад для сборки!<br />Ожидайте смс или звонок от оператора контакт-сEnter по статусу доставки!

</div>
<? endif ?>

<? if ($paymentProvider): ?>
    <p>Через <span class="timer">5</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
    <div class="pt10">
        <?= $page->render('order/form-payment', array('provider' => $paymentProvider, 'order' => reset($orders))) ?>
    </div>
<? else: ?>
    <div class="mt32" style="text-align: center">
        <a class='bBigOrangeButton' href="<?= $page->url('homepage') ?>">Продолжить покупки</a>
    </div>
<? endif ?>

<? if ($isCredit): ?>
    <div id="credit-widget" data-value="<?= $page->json($creditData) ?>"></div>
<? endif ?>


<? if ($isOrderAnalytics) echo $page->render('order/_analytics', array(
    'orders'       => $orders,
    'productsById' => $productsById,
    'servicesById' => $servicesById,
    'shopsById'    => $shopsById,
)) ?>


<? if (\App::config()->smartEngine['push']): ?>
    <? foreach ($orders as $order): ?>
    <?
        $jsonOrdersData = array('order' => array('id' => $order->getId(), 'product' => array_map(function ($orderProduct) {
            /** @var $orderProduct \Model\Order\Product\Entity */
            return array('id' => $orderProduct->getId(), 'price' => $orderProduct->getPrice(), 'quantity' => $orderProduct->getQuantity());
        }, $order->getProduct())))
    ?>
        <div class="product_buy-container" data-url="<?= $page->url('smartengine.push.buy') ?>" data-order="<?= $page->json($jsonOrdersData) ?>"></div>
    <? endforeach ?>
<? endif ?>

<? require __DIR__ . '/partner-counter/_complete.php' ?>
