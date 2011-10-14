<?php foreach ($list as $item): ?>
    <?php include_component('order', 'show', array('view' => 'compact', 'order' => $item['order'])) ?>
<?php endforeach ?>