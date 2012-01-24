<?php slot('title', 'Моя корзина') ?>

<?php if ($cart->countFull() > 0): ?>
  <?php include_component('cart', 'show') ?>

  <!-- Total -->
  <div class="fl font14 gray">
    Для оформления заказа от вас потребуется<br />только имя и телефон для связи.
  </div>
  <div class="basketinfo">
    <div class="left">
      <div class="font16">Сумма заказа:</div>
      <div class="font34"><strong><span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span></strong></div>
    </div>
    <div class="right pt20">
      <a href="<?php echo url_for('order_new') ?>" class="button bigbuttonlink width214 mb15">Оформить заказ</a>
    </div>
    <!--
    <div class="right pt20">
        <a href="<?php echo url_for('cart_clear') ?>" class="button bigbuttonlink width214 mb15">Очистить корзину</a>
    </div>
    -->
  </div>
  <!-- /Total -->

<?php else: ?>
  <p>в корзине нет товаров</p>

<?php endif ?>


<?php slot('seo_counters_advance') ?>
  <?php include_component('cart', 'seo_counters_advance') ?>
<?php end_slot() ?>

<!--Трэкер "Корзина"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Корзина"-->
