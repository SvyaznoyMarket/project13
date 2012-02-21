<?php foreach ($list as $item): ?>
  <?php include_component('productSoa', 'show', array('view' => 'expanded', 'product' => $item)) ?>
<?php endforeach ?>
