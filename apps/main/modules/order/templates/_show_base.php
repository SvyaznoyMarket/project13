    <!-- Basket -->
        <div class="fl width215 mr20"><strong class="font16">Ваш заказ:</strong></div>

        <?php include_component('cart', 'show', array('view' => 'order', )) ?>

        <div class="fr width250 ar">
            <div class="font16">Итого к оплате:</div>
            <div class="font34"><strong><?php include_partial('default/sum', array('sum' => $total+$deliveryPrice, )) ?> <span class="rubl">p</span></strong></div>
        </div>

        <div class="fl">
            <div class="clear width500 pb20"></div>

            <div class="fl width215 font16 mr20">Способ оплаты:</div>
            <div class="fl width430">
                <strong><?php echo $order->PaymentMethod->name ?></strong>
            </div>

            <div class="clear width500 pb15"></div>

            <div class="fl width215 font16 mr20">Получатель:</div>
            <div class="fl width430 pb20">
                <div class="pb5"><strong><?php echo $order->recipient_first_name.' '.$order->recipient_last_name ?></strong></div>
                <div class="pb5"><span class="gray">Телефон:</span> <?php echo $order->recipient_phonenumbers ?></div>
            </div>

            <div class="clear"></div>

            <div class="fl width215 font16 mr20"><?php echo $order->isSelfDelivery() ? 'Где будете получать' : 'Куда доставить' ?>:</div>
            <div class="fl width430 pb20">
                <div class="pb5"><span class="gray">Город:</span> <?php echo $order->Region->name ?></div>
                <?php if (!empty($order->address)): ?><div class="pb5"><span class="gray">Адрес доставки:</span> <?php echo $order->address ?></div><?php endif ?>
                <?php if (!empty($order->shop_id)): ?><div class="pb5"><span class="gray">Забрать из магазина:</span> <?php echo $order->getShop()->name ?></div><?php endif ?>
            </div>

            <div class="clear"></div>

            <?php if (!$order->isSelfDelivery()): ?>
            <div class="fl width215 font16 mr20">Стоимость доставки:</div>
            <div class="fl width430">
                <strong><?php include_partial('default/sum', array('sum' => $deliveryPrice, )) ?> <span class="rubl">p</span></strong>
            </div>
            <div class="clear width500 pb15"></div>
            <?php endif ?>

            <div class="fl width215 font16 mr20">Дополнительная информация:</div>
            <div class="fl width430 pb20">
                <div class="pb5"><?php echo $order->extra ?></div>
            </div>
        </div>

<?php if (false): ?>

<div class="form">

  <div class="form-row">
    <label>Город</label>
    <div class="content"><?php echo $order->Region ?></div>
  </div>

  <div class="form-row">
    <label>Вы покупаете как</label>
    <div class="content"><?php echo ($order->is_legal ? 'юр. лицо' : 'частное лицо') ?></div>
  </div>

</div>


<?php include_component('cart', 'show') ?>

<p>Итого: <?php include_partial('default/sum', array('sum' => $item['sum'])) ?></p>
<?php endif ?>