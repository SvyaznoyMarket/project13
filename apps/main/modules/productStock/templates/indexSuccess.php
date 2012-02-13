<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
<?php end_slot() ?>

<?php slot('title', 'Где купить '.mb_lcfirst($product->name)) ?>

<?php include_component('productStock', 'show', array('product' => $product)) ?>
