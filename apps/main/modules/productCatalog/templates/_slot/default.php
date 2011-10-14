<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>
<?php if ($productCategory->getNode()->hasChildren()): ?>
<?php include_component('productCategory', 'child_list', array('view' => 'carousel', 'productCategory' => $productCategory)) ?>
<?php else: ?>

<?php endif ?>
<div class="clear"></div>
