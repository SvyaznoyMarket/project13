<form id="register-form" action="<?php echo url_for('@user_register') ?>" class="form" method="post">
  <input type="hidden" name="redirect_to" value="<?php echo !empty($redirect) ? $redirect : $sf_request->getUri() ?>" />
  <div class="fr width327 ml20">
    <div class="font16 pb20">Я новый пользователь</div>
    <div class="pb5">Как к вам обращаться?</div>
    <div class="pb5">
      <?php echo $form['first_name']->renderError() ?>
      <?php echo $form['first_name']->render(array('class' => 'text width315 mb10')) ?>
      <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
    </div>
    <div class="pb5">E-mail или мобильный телефон:</div>
    <div class="pb5">
      <?php echo $form['username']->renderError() ?>
      <?php echo $form['username']->render(array('class' => 'text width315 mb10')) ?>
      <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
    </div>
    <label class="bSubscibe checked">
        <b></b> Хочу знать об интересных<br />предложениях
        <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe" checked="checked" />

    </label>
    <input type="submit" class="fr button bigbutton" value="Регистрация" tabindex="10" />
    <div class="clear"></div>
    <p>Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a class="underline" href="/terms">Условиями продажи...</a></p>
    <? if (sfConfig::get('app_guard_corporate_registration_enabled', false)): ?>
        <p><a href="<?php echo url_for('user_corporateRegister') ?>" class="orange underline">регистрация юридического лица</a></p>
    <? endif ?>

  </div>
</form>