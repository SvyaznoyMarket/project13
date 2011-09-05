<form action="<?php echo url_for('@user_changePassword') ?>" method="post">
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Изменить пароль" />
</form>