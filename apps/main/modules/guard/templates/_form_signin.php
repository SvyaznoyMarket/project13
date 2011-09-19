<h2>Войти как пользователь</h2>
<?php include_component('guard', 'oauth_links') ?>

<br class="clear" />

<h2>Войти по логину сайта</h2>
<form class="event-submit" data-event="form.submit" data-reload="true" action="<?php echo url_for('@user_signin') ?>" method="post">
  <ul class="form">
    <?php echo $form ?>
  </ul>

  <p><?php echo link_to('Новый пользователь?', 'user_register') ?></p>

  <p>Пользователь: <strong>79031234567 или client@maxus.ru</strong><br />Пароль: <strong>client</strong></p>

  <input type="submit" value="Вход" />
</form>