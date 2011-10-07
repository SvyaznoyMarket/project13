<?php slot('navigation') ?>
  <?php include_component('productCard', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->name) ?>

<?php include_component('productComment', 'list', array('product' => $product)) ?>