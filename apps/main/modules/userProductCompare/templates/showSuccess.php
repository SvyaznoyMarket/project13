<?php if (false): ?>
<div class="block">
  <?php include_component('user', 'menu') ?>
</div>
<?php endif ?>

<?php slot('title', 'Сравнение товаров: '.$productType) ?>

<!--div class="block"-->
  <?php include_component('userProductCompare', 'show', array('productType' => $productType)) ?>
<!--/div-->