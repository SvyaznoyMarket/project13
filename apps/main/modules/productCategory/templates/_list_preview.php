<!-- Goods -->
<div class="goodslist">
<?php foreach ($productCategoryList as $productCategory): ?>
  <?php echo include_component('productCategory', 'show', array('view' => 'preview', 'productCategory' => $productCategory)) ?>
<?php endforeach ?>
</div>
<!-- /Goods -->