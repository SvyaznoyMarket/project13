<?php foreach ($item['accessory'] as $i => $accessory): ?>
  <?php include_component('product', 'show', array('view' => 'extra_compact', 'product' => $accessory, 'maxPerPage' => 4, 'ii' => $i)) ?>
<?php endforeach ?>