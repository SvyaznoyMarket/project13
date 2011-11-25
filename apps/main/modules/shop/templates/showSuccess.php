<?php slot('title', $shop) ?>

<?php slot('navigation') ?>
  <?php include_component('shop', 'navigation', array('region' => $shop->Region, 'shop' => $shop)) ?>
<?php end_slot() ?>

<?php include_component('shop', 'show', array('shop' => $shop)) ?>