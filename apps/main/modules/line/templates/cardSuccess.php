<?php slot('title', 'Серия '.$line->name) ?>

<?php slot('navigation') ?>
  <?php include_component('line', 'navigation', array('line' => $line)) ?>
<?php end_slot() ?>

<?php include_component('line', 'main_product', array('line' => $line)) ?>
<?php include_partial('line/product_list', $sf_data) ?>
