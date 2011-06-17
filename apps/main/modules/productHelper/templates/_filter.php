<form action="<?php echo url_for('productHelper_result', $sf_data->getRaw('productHelper')) ?>" method="post">

  <ul>
    <?php echo $productHelperFilter ?>
  </ul>

  <input type="submit" value="Показать результаты" />

</form>