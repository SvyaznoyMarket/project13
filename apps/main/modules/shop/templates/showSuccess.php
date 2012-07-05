<?php slot('title', $shop) ?>

<?php slot('navigation') ?>
  <?php include_component('shop', 'navigation', array('region' => $region, 'shop' => $shop)) ?>
<?php end_slot() ?>

<?php include_component('shop', 'show', array('shop' => $shop)) ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('shop', 'seo_counters_advance') ?>
<?php end_slot() ?>