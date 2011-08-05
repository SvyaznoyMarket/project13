<form class="event-submit" data-event="form.submit" action="<?php echo url_for('userProductNotice_create', $sf_data->getRaw('product')) ?>" method="post">
  <?php echo $form ?>

  <input type="submit" value="Подтвердить" />
</form>