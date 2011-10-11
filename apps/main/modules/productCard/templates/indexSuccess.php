<?php slot('navigation') ?>
  <?php include_component('productCard', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->Creator.'  '.$product->name) ?>

  <?php //include_partial('product/name', array('product' => $product)) ?>
  <?php include_component('product', 'show', array('product' => $product)) ?>
  <?php include_component('service', 'listByProduct', array('product' => $product)) ?>

  <?php include_component('productComment', 'list', array(
	  'product' => $product, 
	  'page' => 1, 
	  'sort' => 'rating_desc', 
	  'showSort' => false, 
	  'showPage' => false
  )) ?>

  <?php //echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>

  <?php //echo link_to('Аналогичные товары', 'similarProduct', $sf_data->getRaw('product')) ?>

  <?php //echo link_to('Наличие в сети', 'productStock', $sf_data->getRaw('product')) ?>