<form action="<?php echo $form->isNew() ? url_for('userAddress_create') : url_for('userAddress_update', $form->getObject()) ?>" method="post">
  <ul>
  <?php echo $form ?>
  </ul>

  <input type="submit" value="Сохранить" class="left" />
  <br class="clear" />
</form>