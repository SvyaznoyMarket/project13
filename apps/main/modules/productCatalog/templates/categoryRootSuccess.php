<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
  <?php include_component('productCatalog', 'tag', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('productCatalog/plugs/plug'.$productCategory->position) ?>

<div class="clear"></div>

<?php echo include_component('productCategory', 'child_list', array('view' => 'preview', 'productCategory' => $productCategory)) ?>

<?php //include_component('productCategory', 'productType_list', array('productCategory' => $productCategory)) ?>