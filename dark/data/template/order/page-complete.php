<?php
/**
 * @var $page            \View\Order\CreatePage
 * @var $user            \Session\User
 * @var $orders          \Model\Order\Entity[]
 * @var $productsById    \Model\Product\Entity[]
 * @var $servicesById    \Model\Product\Service\Entity[]
 * @var $shopsById       \Model\Shop\Entity[]
 * @var $isCredit        bool
 * @var $paymentProvider \Payment\ProviderInterface
 * @var $creditData      array
 */
?>

<?php
$isCorporative = $user->getEntity() ? $user->getEntity()->getIsCorporative() : false;
// TODO: доделать
$isCredit = (bool)$creditData;
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
    В ближайшее время мы вам перезвоним :)
    <br/>Специалист нашего Контакт-сENTER уточнит, где и когда будет удобно получить заказ.
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


<? if (\App::config()->analytics) echo $page->render('order/_analytics', array(
    'orders'       => $orders,
    'productsById' => $productsById,
    'servicesById' => $servicesById,
    'shopsById'    => $shopsById,
)) ?>


<?php if (false && sfConfig::get('app_smartengine_push')): ?>
<?php foreach ($orders as $order) {
        $jsonOrdersData = json_encode(array('order' => array('id' => $order['id'], 'product' => array_map(function ($product) {
            return array('id' => $product['product_id'], 'price' => $product['price'], 'quantity' => $product['quantity']);
        }, $order['product']),)), JSON_HEX_QUOT | JSON_HEX_APOS)
        ?>
    <div class="product_buy-container" data-url="<?php echo url_for('smartengine_buy') ?>" data-order='<?php echo $jsonOrdersData ?>'></div>
    <?php
    }
    ?>
<?php endif ?>


<? if (\App::config()->googleAnalytics['enabled']): ?>
    <?= $page->render('order/_odinkodForComplete', array('orders' => $orders)) ?>
<? endif ?>


<? if (\App::config()->googleAnalytics['enabled']): ?>
    <!-- Google Code for successful order1 Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1001659580;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "eUJoCMTF9gQQvLnQ3QM";
        var google_conversion_value = 0;
        /* ]]> */
    </script>
    <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1001659580/?value=0&amp;label=eUJoCMTF9gQQvLnQ3QM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
<? endif ?>


<? if (\App::config()->googleAnalytics['enabled']): ?>
    <!-- Google Code for successful order2 Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1004602214;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "52ISCLrV2gQQ5oaE3wM";
        var google_conversion_value = 0;
        /* ]]> */
    </script>
    <script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1004602214/?value=0&amp;label=52ISCLrV2gQQ5oaE3wM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
<? endif ?>
