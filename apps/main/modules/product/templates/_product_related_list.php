<?php foreach ($order['related'] as $i => $related): ?>
  <?php include_component('product', 'show', array('view' => 'extra_compact', 'product' => $related, 'maxPerPage' => 4, 'ii' => $i)) ?>
<?php endforeach ?>