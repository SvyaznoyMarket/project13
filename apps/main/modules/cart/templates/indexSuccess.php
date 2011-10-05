<?php if (false): ?>
<div class="block wide">

  <?php include_component('user', 'menu') ?>

  <h1>Корзина товаров</h1>

  <div class="block">
    <?php if ($cart->count() > 0): ?>
      <?php include_component('cart', 'show') ?>

      <?php echo link_to('очистить', '@cart_clear', array('class' => 'cart cart-clear')) ?>

      <p><a href="<?php echo url_for('order_new') ?>">Оформить заказ</a></p>

    <?php else: ?>
      <p>в корзине нет товаров</p>

    <?php endif ?>
  </div>

</div>
<?php endif ?>

<?php slot('title', 'Моя корзина') ?>

    <?php if ($cart->count() > 0): ?>
      <?php include_component('cart', 'show') ?>

    <!-- Total -->
    <div class="fl font11 gray">
        <strong>Условия доставки</strong><br />Доставляем по России, Казахстану, Белоруссии<br />Срочная доставка*<br />При покупке свыше 3 000 рублей доставка - бесплатно*<br />При заказе товара в кредит скидки интернет-магазина не предоставляются<br />Купили, не понравился товар, вернем деньги или обменяем<br />Все региональные доставки застрахованы за наш счет
    </div>
    <div class="basketinfo">
        <div class="left">
            <div class="font16">Сумма заказа:</div>
            <div class="font34"><strong><?php echo $cart->getTotal() ?> <span class="rubl">&#8399;</span></strong></div>
        </div>
        <div class="right pt20">
            <a href="<?php echo url_for('order_new') ?>" class="button bigbuttonlink width214 mb15">Оформить заказ</a>
            <div class="pb5"><strong><a href="" class="red underline">Купить быстро в 1 клик</a></strong></div>
            <div class="font11 gray">От вас потребуется только имя и телефон для связи.</div>
        </div>
    </div>
    <!-- /Total -->

    <?php else: ?>
      <p>в корзине нет товаров</p>

    <?php endif ?>
