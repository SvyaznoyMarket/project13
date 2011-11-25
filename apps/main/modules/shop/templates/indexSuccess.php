<?php slot('title', 'Магазины Enter') ?>

<?php slot('navigation') ?>
  <?php include_component('shop', 'navigation', array('region' => $region)) ?>
<?php end_slot() ?>

<?php include_component('shop', 'map', array('region' => $region)) ?>
<?php include_component('shop', 'list', array('region' => $region)) ?>

<div class="clear"></div>