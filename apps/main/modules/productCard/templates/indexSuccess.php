<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
<?php end_slot() ?>

<div class="block">
  <?php //include_partial('product/name', array('product' => $product)) ?>
  <?php slot('title', $product->Creator.'  '.$product->name) ?>
  <?php include_component('product', 'show', array('product' => $product)) ?>
  <?php include_component('service', 'listByProduct', array('product' => $product)) ?>
</div>

<div class="block">
  <?php echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>
</div>

<div class="block">
  <?php echo link_to('Аналогичные товары', 'similarProduct', $sf_data->getRaw('product')) ?>
</div>

<div class="block">
  <?php echo link_to('Наличие в сети', 'productStock', $sf_data->getRaw('product')) ?>
</div>
