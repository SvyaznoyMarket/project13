<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->name) ?>

<?php include_partial('product', array('product' => $product)) ?>

<?php include_component('productComment', 'list', array('product' => $product, 'page' => $page, 'sort' => $sort, 'showSort' => true, 'showPage' => true)) ?>