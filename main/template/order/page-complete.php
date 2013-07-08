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
    <p class="title-font16 font16">Сейчас он отправлен на склад для сборки!<br/>
Ожидайте смс или звонок от оператора контакт-сEnter по статусу доставки!</p>
    <p class="font19">Номер заказа: <?= $order->getNumber() ?></p>

    <? if ($order->getDeliveredAt() instanceof \DateTime): ?>
        <p class="font16">Дата доставки: <?= $order->getDeliveredAt()->format('d.m.Y') ?></p>
    <? endif ?>

    <p class="font16">Сумма заказа: <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></p>
    <p class="font16">Сумма для оплаты: <span id="paymentWithCard"><?= $page->helper->formatPrice($order->getPaySum()) ?></span> <span class="rubl">p</span></p>
    <div class="line pb15"></div>
<?php endforeach ?>

<?
//$form = \View\Order\Form::get....
//print_r($form);
//print_r($user->getFirstName());
?>
<?= $page->render('partner-counter/_get4click', ['order' => $order] ) ?>

<? if ($isCorporative): ?>
    <div class="mt32">
        В ближайшее время мы оповестим вас о выставлении счета в <strong><a href="<?= $page->url('user.order') ?>">личном кабинете</a></strong>.
    </div>
<? endif ?>

<? // if (!$isCredit && !$isCorporative): ?>
<div class="mt32 clearfix socnet-ico-box">
    <ul class="socnet-ico-list">
        <li class="socnet-ico-list__yam"><a target="_blank" class="socnet-ico-list-link" data-type="Yandex Market" href="http://www.enter.ru/market" ></a></li>
        <li class="socnet-ico-list__fs"><a target="_blank" class="socnet-ico-list-link" data-type="Foursquare" href="http://ru.foursquare.com/enter_ru"></a></li>
        <li class="socnet-ico-list__inst"><a target="_blank" class="socnet-ico-list-link" data-type="Instagram" href="http://instagram.com/enterllc"></a></li>
        <li class="socnet-ico-list__yt"><a target="_blank" class="socnet-ico-list-link" data-type="YouTube" href="http://www.youtube.com/user/EnterLLC"></a></li>
        <li class="socnet-ico-list__vk"><a target="_blank" class="socnet-ico-list-link" data-type="Vkontakte" href="http://vk.com/youcanenter"></a></li>
        <li class="socnet-ico-list__tw"><a target="_blank" class="socnet-ico-list-link" data-type="Twitter" href="http://twitter.com/enter_ru"></a></li>
        <li class="socnet-ico-list__fb"><a target="_blank" class="socnet-ico-list-link" data-type="FaceBook" href="http://www.facebook.com/enter.ru"></a></li>
    </ul>
</div>
<? // endif ?>

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


<? if (\App::config()->smartengine['push']): ?>
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

<?= $page->tryRender('order/partner-counter/_complete', [
    'orders'       => $orders,
    'productsById' => $productsById,
]) ?>
