<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $product, 'isComment' => true)) ?>
<?php end_slot() ?>

<?php slot('title', $product->getName().': отзывы покупателей') ?>

<?php include_partial('product', array('product' => $product)) ?>

<?php include_component('productComment', 'form', array('product' => $product)) ?>
