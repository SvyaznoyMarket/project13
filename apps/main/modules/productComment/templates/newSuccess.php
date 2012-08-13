<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $item, 'isComment' => true)) ?>
<?php end_slot() ?>

<?php slot('title', $item->getName().': отзывы покупателей') ?>

<?php include_partial('product', array('product' => $item)) ?>

<?php include_component('productComment', 'form', array('product' => $item)) ?>
