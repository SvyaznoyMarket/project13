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
 * @var $sessionIsReaded    bool
 * @var $paymentMethod      \Model\PaymentMethod\Entity
 * @var $form               \View\Order\Form
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$isCorporative = $user->getEntity() ? $user->getEntity()->getIsCorporative() : false;
// TODO: доделать
$isCredit = (bool)$creditData;
$isOrderAnalytics = isset($isOrderAnalytics) ? $isOrderAnalytics : true;

if (!isset($paymentUrl)) $paymentUrl = null;
?>

<? if (isset($form)): ?>
    <div class="js-orderData" data-value="<?= $helper->json([
        'email' => $form->getEmail(),
        'subscribe' => $form->getSubscribe(),
        'sessionIsReaded' => $sessionIsReaded,
    ]) ?>"></div>
<? endif ?>

<!-- Header -->
<div class='bBuyingHead clearfix'>
    <a class="bBuyingHead__eLogo" href="<?= $page->url('homepage') ?>"></a>

    <div class="bBuyingHead__eTitle">
        <span class="bSubTitle">Оформление заказа</span><br>
        <span class="bTitle"><?= $isCredit ? 'Покупка в кредит' : 'Спасибо за ваш заказ!' ?></span>
    </div>
</div>
<!-- /Header -->

<? foreach ($orders as $order): ?>
    <? if ($order->getIsPartner()) : ?>
        <p class="title-font16 font16">
        <? if ($order->seller && $order->seller->ui == 'c562d9cb-cfd7-11e1-be71-3c4a92f6ffb8') : ?>
            Ваш заказ передан продавцу, он обязательно свяжется с Вами.<br/>
            <b>Продавец:</b> Закрытое акционерное общество «Связной Логистика».<br/>
            Адрес: 115280, г. Москва, ул. Ленинская слобода, д. 19<br/>
            ИНН: 7703567318, ОГРН 1057748731336<br/>
            Интернет-магазин «Связной» 8 (800) 700 43 43
        <? else : ?>
            Ваш заказ принят, наш оператор свяжется с Вами, уточнит детали заказа и согласует условия доставки.
            Доставку будет осуществлять компания-партнер <?= $order->seller->name ?>.
        <? endif; ?>
        </p>
    <? else : ?>
        <p class="title-font16 font16">
            Сейчас он отправлен на склад для сборки!<br/>
            Ожидайте смс или звонок от оператора контакт-сEnter по статусу заказа!
        </p>
    <? endif; ?>
    <p class="font19">Номер заказа: <?= $order->getNumberErp() ?></p>

    <? if ($order->getDeliveredAt() instanceof \DateTime): ?>
        <p class="font16">Дата доставки: <?= $order->getDeliveredAt()->format('d.m.Y') ?></p>
    <? endif ?>

    <? if ($order->getSum()): ?>
        <p class="font16">Сумма заказа: <span class="mBold"><?= $page->helper->formatPrice($order->getSum()) ?></span> <span class="rubl">p</span></p>
    <? endif ?>

    <? if ($order->getPaySum()): ?>
        <p class="font16">Сумма для оплаты: <span class="mBold" id="paymentWithCard"><?= $page->helper->formatPrice($order->getPaySum()) ?></span> <span class="rubl">p</span></p>
    <? endif ?>

    <? if ($paymentMethod): ?>
        <p class="font16" style="border-bottom: 1px solid #e6e6e6; margin: 0 0 30px; padding-bottom: 10px;">Способ оплаты: <span class="mBold"><?= $paymentMethod->getName() ?></span></p>
    <? endif ?>

    <div class="line pb15"></div>
<?php endforeach ?>


<? if ($isCorporative): ?>
    <div class="mt32">
        В ближайшее время мы оповестим вас о выставлении счета в <strong><a href="<?= $page->url('user.orders') ?>">личном кабинете</a></strong>.
    </div>
<? endif ?>

<? if ($paymentProvider || $paymentUrl || ($paymentMethod && $paymentMethod->isWebmoney())): ?>
    <p>Через <span class="timer">5</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
    <div class="pt10">
        <?= $page->render('order/form-payment', [
            'provider' => $paymentProvider,
            'form' => isset($paymentForm) ? $paymentForm : null,
            'order' => reset($orders),
            'paymentUrl' => $paymentUrl,
            'paymentMethod' => $paymentMethod
        ]) ?>
    </div>
<? else: ?>
    <div class="mt32" style="text-align: center">
        <? if ($isCredit): ?>
            <a class='bBigOrangeButton jsCreditBtn' href="#">Перейти к оформлению кредита</a>
            <p>Виджет оформления кредита откроется автоматически через несколько секунд</p>
        <? else: ?>
            <a class='bBigOrangeButton' href="<?= $page->url('homepage') ?>">Продолжить покупки</a>
        <? endif ?>
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


<? if ($isCredit): ?>
    <div id="credit-widget" data-value="<?= $page->json($creditData) ?>"></div>
<? endif ?>

<? if ($isOrderAnalytics) echo $page->render('order/_analytics', array(
    'orders'       => $orders,
    'productsById' => $productsById,
    'servicesById' => $servicesById,
    'shopsById'    => $shopsById,
    'paymentMethod' => $paymentMethod
)) ?>

<? if (!$sessionIsReaded) {
    // Если сесиия уже была прочитана, значит юзер обновляет страницу, не трекаем партнёров вторично
    echo $page->tryRender('order/partner-counter/_complete', [
        'orders'       => $orders,
        'productsById' => $productsById,
    ]);
    echo $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $productsById]);
} ?>