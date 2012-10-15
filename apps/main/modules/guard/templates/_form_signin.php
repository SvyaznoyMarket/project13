<form id="login-form" action="<?php echo url_for('@user_signin') ?>" class="form" method="post">
  <input type="hidden" name="redirect_to" value="<?php echo !empty($redirect) ? $redirect : $sf_request->getUri() ?>" />
  <div class="loginForm fl width327">
    <div class="font16 pb20">У меня есть логин и пароль</div>

    <div class="pb5">E-mail или мобильный телефон:</div>
    <div class="pb5">
      <?php echo $form['username']->renderError() ?>
      <?php echo $form['username']->render(array('class' => 'text width315 mb10')) ?>
      <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
    </div>

    <div class="pb5"><a id="forgot-pwd-trigger" href="<?php echo url_for('user_forgotPassword') ?>" class="fr orange underline">Забыли пароль?</a>Пароль:</div>
    <div class="pb5">
      <?php echo $form['password']->renderError() ?>
      <?php echo $form['password']->render(array('class' => 'text width315 mb10')) ?>
      <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
    </div>

    <input type="submit" class="fr button bigbutton" value="Войти" tabindex="4" />

    <!--
    <div class="ml20 pt10">
      <label for="checkbox-8" class="prettyCheckbox checkbox list"><span class="holderWrap" style="width: 13px; height: 13px;"><span class="holder" style="width: 13px;"></span></span>Запомнить меня на этом компьютере</label>
      <?php //echo $form['remember']->render(array('class' => 'hiddenCheckbox', 'id' => 'checkbox-8')) ?>
    </div>
    -->

  </div>
</form>