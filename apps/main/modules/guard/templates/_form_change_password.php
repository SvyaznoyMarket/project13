<?php
  $user = $sf_user->getGuardUser();
  $text = array();

  if (!empty($user->email))
  {
    $text[] = 'письмо';
  }
  if (!empty($user->phonenumber))
  {
    $text[] = 'SMS';
  }

  $help = count($text) ? strtr('Внимание! После смены пароля Вам придет %%text%% с новым паролем', array('%%text%%' => implode(' и ', $text))) : false;
?>

<form action="<?php echo url_for('@user_changePasswordSave') ?>" method="post">
  <div class="fl width430">

<!--        <div class="pb20"><strong>Чтобы изменить пароль, укажите свой текущий пароль</strong></div>-->

    <?php echo $form->renderHiddenFields() ?>

    <div class="pr fr">
      <div class="help">
        <br/><br/><br/>
        Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.
      </div>
    </div>

    <div class="pb10">
      <?php echo $form['password_old']->renderLabel() ?>:
      <?php echo $form['password_old']->renderError() ?>
    </div>
    <?php echo $form['password_old']->render() ?>

    <div class="pb10">
      <?php echo $form['password_new']->renderLabel() ?>:
      <?php echo $form['password_new']->renderError() ?>
    </div>
    <?php echo $form['password_new']->render() ?>

    <div class="clear pb10"></div>
    <div class="pb20"><input type="submit" class="button yellowbutton" id="bigbutton" value="Сохранить изменения" /></div>

    <?php if ($help): ?>
      <div class="attention font11"><?php echo $help ?></div>
    <?php endif ?>

  </div>
</form>

<?php echo $form->renderGlobalErrors(); // var_dump()?>