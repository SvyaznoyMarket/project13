<?php slot('navigation') ?>
  <?php include_component('productCatalog_', 'navigation', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>
<?php end_slot() ?>

<?php slot('title', 'Где купить '.mb_lcfirst($product->name)) ?>

<?php slot('after_body_block') ?>
<?php render_partial('product_/templates/_oneclickTemplate.php') ?>
<?php end_slot() ?>

<?php include_component('productStock', 'show', array('product' => $product)) ?>
