    
<!-- Shop -->
<h2 class="bold">Где купить <?php echo $product ?></h2>
<div class="line pb15"></div>
<div class="descriptionlist shoplist">
  <div class="point">
    <div class="title"><h3>Интернет магазин www.enter.ru (Доставка по всей России)</h3></div>
    <div class="description"><b class="supply1"></b>Много</div>

  </div>

  <div class="pb15"><strong>Москва:</strong></div>
  
  <?php foreach ($list as $item): ?>
  <div class="point">
    <div class="title"><h3><?php echo $item['name'] ?></h3></div>
    <div class="description">
      <?php include_partial('productStock/quantity', array('quantity' => $item['quantity'])) ?>
    </div>
  </div>
  <?php endforeach ?>

</div>
<!-- /Shop  -->

<?php foreach ($list as $time): ?>

<?php endforeach; ?>
