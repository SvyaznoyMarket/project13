<?php foreach ($product->related as $i => $related): ?>
  <?php include_component('productSoa', 'show', array('view' => 'extra_compact', 'product' => $related, 'maxPerPage' => 4, 'ii' => $i)) ?>
<?php endforeach ?>