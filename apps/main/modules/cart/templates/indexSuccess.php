<?php slot('title', 'Моя корзина') ?>

<?php
if ($cart->countFull() > 0): ?>
  <?php include_component('cart', 'show') ?>

  <?php render_partial('cart/templates/_cart_make_order_button.php', array('cart' => $cart, 'selectCredit' => $selectCredit)) ?>

  <?php render_partial('cart/templates/_footer.php', array()) ?>

<?php else: ?>
  <p>в корзине нет товаров</p>

<?php endif ?>


<?php slot('seo_counters_advance') ?>
  <?php include_component('cart', 'seo_counters_advance', array('cart' => $cart)) ?>
<?php end_slot() ?>

<!--Трэкер "Корзина"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Корзина"-->
