<? if (Session\AbTest\AbTest::isSelfPaidDelivery()) : ?>
    <div class="cartInfo"
         data-bind="visible: cart().sum() != 0 && cart().sum() < ENTER.config.pageConfig.selfDeliveryLimit"
         style="display: <?= \App::config()->self_delivery['limit'] > $cart->getSum() ? 'block' : 'none' ?>">
        Для бесплатного самовывоза добавьте товаров на <strong><span data-bind="text: ENTER.config.pageConfig.selfDeliveryLimit - cart().sum()"><?= \App::config()->self_delivery['limit'] - $cart->getSum()?></span> <span class="rubl">p</span></strong>
    </div>
<? endif; ?>
 