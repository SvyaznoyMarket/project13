<form action="<?php echo $form->isNew() ? url_for('userAddress_create') : url_for('userAddress_update', $form->getObject()) ?>" method="post">
  <?php echo $form ?>

  <input type="submit" value="Сохранить" class="left" />
  <button class="left" formaction="<?php echo url_for('userAddress') ?>" >Отменить</button>
  <br class="clear" />
</form>