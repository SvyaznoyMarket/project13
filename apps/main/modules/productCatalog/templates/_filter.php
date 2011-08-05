<form class="product_filter-block" action="<?php echo $url ?>" method="get" data-action-count="<?php echo url_for('productCatalog_count', $sf_data->getRaw('productCategory')) ?>">
  <ul class="form">
    <?php echo $productFilter ?>
  </ul>

  <input type="submit" value="Подобрать" />
</form>