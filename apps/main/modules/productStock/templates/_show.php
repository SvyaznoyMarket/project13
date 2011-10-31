
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
