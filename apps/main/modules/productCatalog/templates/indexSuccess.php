<?php slot('title', 'Каталог товаров') ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation') ?>
<?php end_slot() ?>

<?php include_component('productCatalog', 'category_list', array('productCategoryList' => $productCategoryList)) ?>
