<?php
/** @var $cart UserCartNew */
?>

<?php slot('title', 'Моя корзина') ?>

<div style="float:left; width: 100%; padding-bottom: 20px">
  <div id="adfox920" class="adfoxWrapper"></div>
</div>
<?php if ($cart->countFull() > 0): ?>

  <?php include_component('cart_', 'show') ?>

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
<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: baskets PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=baskets&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=baskets&bt=21&pz=0&rnd=1616108824" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<div id="heiasOrder" data-vars="<?php echo $cart->getSeoCartArticle(); ?>" class="jsanalytics"></div>
<?php end_slot() ?>

<!--Трэкер "Корзина"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Корзина"-->
