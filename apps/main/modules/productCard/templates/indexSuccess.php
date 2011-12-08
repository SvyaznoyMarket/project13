<?php slot('header_meta_og') ?>
  <?php include_component('productCard', 'header_meta_og', array('product' => $product) ) ?>
<?php end_slot() ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
<?php end_slot() ?>

<?php slot('title', $product->name) ?>

  <?php //include_partial('product/name', array('product' => $product)) ?>
  <?php include_component('product', 'show', array('product' => $product)) ?>
  <?php #include_component('service', 'listByProduct', array('product' => $product)) ?>

<?php if ($product->isKit()): ?>
  <?php include_partial('product/kit', $sf_data) ?>
<?php endif ?>
  <?php /*include_component('productComment', 'list', array(
	  'product' => $product,
	  'page' => 1,
	  'sort' => 'rating_desc',
	  'showSort' => false,
	  'showPage' => false
  )) */ ?>

  <?php //echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>

  <?php //echo link_to('Аналогичные товары', 'similarProduct', $sf_data->getRaw('product')) ?>

  <?php //echo link_to('Наличие в сети', 'productStock', $sf_data->getRaw('product')) ?>


  <?php include_component('productCatalog', 'navigation_seo', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
