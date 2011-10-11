<?php foreach ($list as $item): ?>
  <?php echo include_component('productCategory', 'show', array('view' => 'carousel', 'productCategory' => $item['productCategory'])) ?>
<?php endforeach ?>
