<? if (Session\AbTest\AbTest::isSelfPaidDelivery()) : ?>
    <div class="cartInfo"
         data-bind="visible: cartSum() != 0 && cartSum() < ENTER.config.pageConfig.selfDeliveryLimit"
         style="display: <?= \App::config()->self_delivery['limit'] > $cart->getSum() ? 'block' : 'none' ?>">
        Для бесплатного самовывоза добавьте товаров на <strong><span data-bind="text: ENTER.config.pageConfig.selfDeliveryLimit - cartSum()"><?= \App::config()->self_delivery['limit'] - $cart->getSum()?></span> <span class="rubl">p</span></strong>
    </div>
<? endif; ?>
 