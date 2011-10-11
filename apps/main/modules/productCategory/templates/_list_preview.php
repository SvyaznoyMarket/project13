<!-- Goods -->
<div class="goodslist">
<?php foreach ($list as $item): ?>
  <?php echo include_component('productCategory', 'show', array('view' => 'preview', 'productCategory' => $item['productCategory'])) ?>
<?php endforeach ?>
</div>
<!-- /Goods -->