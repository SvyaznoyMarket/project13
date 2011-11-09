<?php slot('title', mb_ucfirst($tag)) ?>

<?php slot('navigation') ?>
  <?php include_component('tag', 'navigation', array('tag' => $tag)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<form id="filter_product_type-form" action="<?php echo url_for('tag_show', array('tag' => $tag->token)) ?>" method="get">
  <?php //include_component('product', 'filter_productType', array('productTypeList' => $productTypeList)) ?>
</form>
<?php end_slot() ?>

<?php echo include_partial('productCatalog/product_list', $sf_data) ?>
