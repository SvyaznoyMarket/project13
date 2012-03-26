<?php foreach ($list as $item): ?>
<?php include_component('product', 'show', array('view' => 'expanded', 'product' => $item)) ?>
<?php endforeach ?>
