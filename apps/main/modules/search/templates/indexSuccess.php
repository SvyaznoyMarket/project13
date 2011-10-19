<?php slot('title', mb_strtoupper('Результаты поиска')) ?>

<?php slot('navigation') ?>
  <?php include_component('search', 'navigation', array('searchString' => $searchString)) ?>
<?php end_slot() ?>

<?php slot('page_head') ?>
  <?php include_partial('search/page_head', $sf_data) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('search', 'category', array()) ?>
<?php end_slot() ?>

<?php include_partial('productCatalog/product_list', array('productPager' => $pagers['product'])) ?>
