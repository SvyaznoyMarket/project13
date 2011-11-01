<!-- Goods info -->
<div class="goodsphotosmall"><div class="photo"><img src="<?php echo $product->getMainPhotoUrl(2) ?>" alt="" width="163" height="163" title="" /></div></div>
<div class="fr width219">
  <div class="font11 gray pb10">Перейти в:</div>
  <div class="articlemenu">
    <ul>
      <li><a href="<?php echo url_for('productCard', $sf_data->getRaw('product')) ?>">Карточка товара</a></li>
      <li><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>">Отзывы пользователей</a></li>
      <li class="next"><a href="<?php echo url_for('productStock', $sf_data->getRaw('product')) ?>" class="current">Где купить в магазинах</a></li>
    </ul>
  </div>
</div>

<?php include_component('product', 'show', array('view' => 'description', 'product' => $product)) ?>
<!-- /Goods info -->

<div class="clear pb20"></div>


<!-- Shop -->
<h2 class="bold">Где купить <?php echo $product ?></h2>
<div class="line pb15"></div>
<div class="descriptionlist shoplist">
  <!--
  <div class="point">
    <div class="title"><h3>Интернет магазин www.enter.ru (Доставка по всей России)</h3></div>
    <div class="description"><b class="supply1"></b>Много</div>

  </div>
  -->

  <?php foreach ($list as $item): ?>
  <div class="pb15"><strong><?php echo $item['name'] ?>:</strong></div>

    <?php foreach ($item['shops'] as $shop): ?>
    <div class="point">
      <div class="title"><h3><?php echo $shop['name'] ?></h3></div>
      <div class="description">
        <?php include_partial('productStock/quantity', array('quantity' => $shop['quantity'])) ?>
      </div>
    </div>
    <?php endforeach ?>
  <?php endforeach ?>

</div>
<!-- /Shop  -->

<?php foreach ($list as $time): ?>

<?php endforeach; ?>
