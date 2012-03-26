<?php foreach ($product->accessories as $i => $accessory): ?>
<?php include_component('productSoa', 'show', array('view' => 'extra_compact', 'product' => $accessory, 'maxPerPage' => 4, 'ii' => $i)) ?>
<?php endforeach ?>