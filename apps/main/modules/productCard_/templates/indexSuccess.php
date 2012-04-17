<?php /* @var $product ProductEntity */ ?>

<?php slot('header_meta_og') ?>
  <?php //include_component('productCard', 'header_meta_og', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('navigation') ?>
  <?php //include_component('productCatalog', 'navigation', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
<?php end_slot() ?>

<?php slot('title', $product->getName()) ?>

<?php include_partial('product_/show_card', $sf_data) ?>

<br class="clear"/>

<?php include_partial('productCard_/seo', $sf_data) ?>
