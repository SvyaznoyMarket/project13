<form action="<?php echo url_for('productCatalog_filter', $sf_data->getRaw('productCategory')) ?>" method="get">
  <ul class="form">
    <?php echo $productFilter ?>
  </ul>

  <input type="submit" value="Подобрать" />
</form>