<div class="block">
  <div class="left">всего: <?php echo count($productList) ?></div>
  <?php include_component('product', 'list_view') ?>
  <br class="clear" />
</div>

<div class="block">
  <?php include_component('product', 'list', array('list' => $productList)) ?>
</div>

<div class="block">
  <div class="left">всего: <?php echo count($productList) ?></div>
  <?php include_component('product', 'list_view') ?>
  <br class="clear" />
</div>
