<?php foreach ($list as $item): ?>
  <?php include_component('productCategory', 'show', array('view' => 'carousel', 'productCategory' => $item['productCategory'])) ?>
<?php endforeach ?>
