<?php if (false): ?>

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

<?php endif ?>


<div class="registerbox">
  <form action="<?php echo url_for('@user_signin') ?>" class="form" method="post">
    <div class="fl width327 mr20">
      <div class="font16 pb20">У меня есть логин и пароль</div>

      <div class="pb5">E-mail или мобильный телефон:</div>
      <div class="pb5">
        <?php echo $formSignin['username']->render(array('class' => 'text width315 mb10')) ?>
        <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
      </div>

      <div class="pb5"><a href="<?php echo url_for('user_forgotPassword') ?>" class="fr orange underline">Забыли пароль?</a>Пароль:</div>
      <div class="pb5">
        <?php echo $formSignin['password']->render(array('class' => 'text width315 mb10')) ?>
        <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
      </div>

      <input type="submit" class="fr button bigbutton" value="Войти" tabindex="4" />
      <div class="ml20 pt10">
        <label for="checkbox-8" class="prettyCheckbox checkbox list"><span class="holderWrap" style="width: 13px; height: 13px;"><span class="holder" style="width: 13px;"></span></span>Запомнить меня на этом компьютере</label>
        <?php echo $formSignin['remember']->render(array('class' => 'hiddenCheckbox', 'id' => 'checkbox-8')) ?>
        <!--<input type="checkbox" value="checkbox-1" name="checkbox-3" id="checkbox-8" class="hiddenCheckbox">-->
      </div>
    </div>
  </form>

  <form action="<?php echo url_for('@user_register') ?>" class="form" method="post">
    <div class="fr width327 ml20">
      <div class="font16 pb20">Я новый пользователь</div>
      <div class="pb5">Как к вам обращаться?</div>
      <div class="pb5">
        <?php echo $formRegister['first_name']->render(array('class' => 'text width315 mb10')) ?>
        <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
      </div>
      <div class="pb5">E-mail или мобильный телефон:</div>
      <div class="pb5">
        <?php echo $formRegister['username']->render(array('class' => 'text width315 mb10')) ?>
        <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
      </div>
      <input type="submit" class="fr button bigbutton" value="Регистрация" tabindex="10" />

    </div>
  </form>

  <div class="clear"></div>
</div>
