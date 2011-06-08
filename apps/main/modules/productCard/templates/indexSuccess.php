<div class="block">
  <?php include_component('product', 'show', array('product' => $product)) ?>
</div>

<div class="block">
  <?php echo link_to('Комментарии', 'productComment', $sf_data->getRaw('product')) ?>
</div>
