<?php slot('title', 'Магазины Enter') ?>

<?php slot('navigation') ?>
  <?php include_component('shop', 'navigation', array('region' => $region)) ?>
<?php end_slot() ?>

<?php include_component('shop', 'map', array('region' => $region)) ?>
<?php include_component('shop', 'list', array('region' => $region)) ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('shop', 'seo_counters_advance') ?>
<?php end_slot() ?>
<div class="clear"></div>