<?php slot('header_meta_og') ?>
<?php include_component('productCardSoa', 'header_meta_og', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('navigation') ?>
<?php include_component('productCardSoa', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->name) ?>

<?php //include_partial('product/name', array('product' => $product)) ?>
<?php include_component('productSoa', 'show', array('product' => $product)) ?>
<?php #include_component('service', 'listByProduct', array('product' => $product)) ?>

<?php if ('kit' == $product->view): ?>
  <?php //include_partial('productSoa/kit', $sf_data) ?>
  <?php include_component('productSoa', 'kit', array('product' => $product)) ?>

  <div class="clear pb25"></div>

  <h2 class="bold"><?php echo $product->name ?> - Характеристики</h2>
  <div class="line pb25"></div>
  <div class="descriptionlist">
    <?php include_component('productSoa', 'property_grouped', array('product' => $product)) ?>
  </div>

  <?php include_component('productSoa', 'tags', array('product' => $product)) ?>
<?php endif ?>
<?php /* include_component('productComment', 'list', array(
  'product' => $product,
  'page' => 1,
  'sort' => 'rating_desc',
  'showSort' => false,
  'showPage' => false
  )) */ ?>

<?php //echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>

<?php //echo link_to('Аналогичные товары', 'similarProduct', $sf_data->getRaw('product')) ?>

<?php //echo link_to('Наличие в сети', 'productStock', $sf_data->getRaw('product')) ?>

<br class="clear" />

<?php include_component('productCatalog', 'navigation_seo', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>


<?php slot('seo_counters_advance') ?>
<?php

$rotCat = $product->getMainCategory();
include_component('productCategory', 'seo_counters_advance', array('unitId' => $rotCat['root_id']))
?>
<?php end_slot() ?>