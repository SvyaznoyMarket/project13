<form action="<?php echo url_for('productComment_create', $sf_data->getRaw('product')) ?>" method="post">
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Оставить комментарий" />
</form>