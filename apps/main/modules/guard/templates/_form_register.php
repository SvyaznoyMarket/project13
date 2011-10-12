<?php if (false): ?>
<form class="event-submit" data-event="form.submit" data-reload="true" action="<?php echo url_for('@user_register') ?>" method="post">
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Регистрация" />
</form>
<?php endif ?>