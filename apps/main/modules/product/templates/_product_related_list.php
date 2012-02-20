<?php foreach ($item['related'] as $i => $related): ?>
  <?php include_component('product', 'show', array('view' => 'extra_compact', 'product' => $related, 'maxPerPage' => $item['related_pager']->getMaxPerPage(), 'ii' => $i)) ?>
<?php endforeach ?>