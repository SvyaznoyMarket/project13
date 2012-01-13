<?php foreach ($productCategoryList as $productCategory): ?>
  <?php include_component('productCategory', 'show', array('view' => 'carousel', 'productCategory' => $productCategory)) ?>
<?php endforeach ?>
