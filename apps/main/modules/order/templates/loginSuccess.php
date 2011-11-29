<?php slot('title', 'Оформление заказа - Шаг 1') ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation') ?>
<?php end_slot() ?>

<?php slot('receipt') ?>
  <?php include_component('order', 'receipt') ?>
<?php end_slot() ?>

<?php slot('step') ?>
<ul class="steplist steplist2">
  <li><a href="<?php echo url_for('order_login') ?>"><span>Шаг 1</span>Данные<br />покупателя</a></li>
  <li><span>Шаг 2</span>Способ доставки<br />и оплаты</li>
  <li class="last"><span>Шаг 3</span>Подтверждение<br />заказа</li>
</ul>
<?php end_slot() ?>

<?php if ($sf_user->isAuthenticated()): ?>
  <div class="fl width215 mr20">
    <div class="pb40"><strong class="font16">Данные покупателя:</strong></div>
  </div>
  <div class="fl width430">
    <span class="font16">
      Вы уже вошли на сайт как <b><?php echo $sf_user ?></b>
      <br/>
      и можете <a href="<?php echo url_for('order_new') ?>" class="orange underline">продолжить оформление</a> заказа.
    </span>
    <br/>
    <br/>
    <br/>
    Если это не ваши данные, то <a href="<?php echo url_for('user_signout') ?>?redirect_to=<?php echo url_for('order_login') ?>" class="underline">войдите на сайт</a> под другим именем.
  </div>
<?php else: ?>
  <!-- Form -->
  <form action="" method="post" class="form" id="form-step-1">
    <input type="hidden" name="redirect_to" value="<?php echo url_for('order_new') ?>"/>
    <div class="fl width215 mr20">
      <div class="pb40"><strong class="font16">Данные покупателя:</strong></div>
      <!--div class="gray pb10">Заполнить данные, используя</div>
      <ul class="backetsharelist">
          <li><a href="" class="facebook">Facebook</a></li>
          <li><a href="" class="vkontakte">Вконтакте</a></li>
          <li><a href="" class="mailru">Mail.ru</a></li>
          <li><a href="" class="odnoklassniki">Одноклассники</a></li>
          <li><a href="" class="twitter">Twitter</a></li>
      </ul-->
    </div>

    <div class="fl width430">

      <ul class="checkboxlist pb10">
        <li class="font16"><label for="radio-1">Уже покупали у нас?</label><input id="radio-1" name="zzz" type="radio" value="login" /></li>
        <li class="font16"><label for="radio-2">Я покупаю впервые!</label><input id="radio-2" name="zzz" type="radio" value="register" /></li>
      </ul>

      <div id="old-user" style="display:none;">
        <div class="pb10"></div>
        <div class="pb10">E-mail или мобильный телефон:</div>
        <?php if ($formSignin['username']->hasError()): ?><div class="pb10 red"><?php echo $formSignin['username']->renderError() ?></div><?php endif ?>
        <?php echo $formSignin['username']->render(array('class' => 'text width418 mb10')) ?>
        <div class="attention font11 gray mb15">Логином может являться номер мобильного телефона или адрес электронной почты. Например: 89101234567 или primer@email.ru</div>

        <div class="pb5"><a id="auth_forgot-link" href="<?php echo url_for('user_forgotPassword') ?>" class="fr orange underline">Забыли пароль?</a>Пароль:</div>
        <?php if ($formSignin['password']->hasError()): ?><div class="pb10 red"><?php echo $formSignin['password']->renderError() ?></div><?php endif ?>
        <?php echo $formSignin['password']->render(array('class' => 'text width418 mb10')) ?>
        <div class="attention font11 gray mb15">Поменять пароль на удобный именно вам можно в <a id="auth-link" class="gray" href="<?php echo url_for('user') ?>" style="display: inline"><strong>личном кабинете</strong></a></div>

        <?php //echo $formSignin['remember']->render(array('class' => 'hiddenCheckbox', 'id' => 'checkbox-8')) ?>
      </div>

      <div id="new-user" style="display:none;">
        <div class="pb10"></div>

        <div class="pb10">E-mail или мобильный телефон:</div>
        <?php if ($formRegister['username']->hasError()): ?><div class="pb10 red"><?php echo $formRegister['username']->renderError() ?></div><?php endif ?>
        <?php echo $formRegister['username']->render(array('class' => 'text width418 mb10')) ?>
        <div class="attention font11 gray mb15">Логином может являться номер мобильного телефона или адрес электронной почты. Например: 89101234567 или primer@email.ru</div>

        <div class="pb10">Как к вам обращаться?</div>
        <?php if ($formRegister['first_name']->hasError()): ?><div class="pb10 red"><?php echo $formRegister['first_name']->renderError() ?></div><?php endif ?>
        <?php echo $formRegister['first_name']->render(array('class' => 'text width418 mb10')) ?>
        <div class="attention font11 gray mb15">Эти данные необходимы для регистрации в системе и оформления платежа. Вам будет выслан пароль на ваш e-mail или моб. телефон</div>

        <!--div class=" pb15">
        <?php //echo $form['is_receive_sms']->render(array('row_class' => 'checkboxlist2', )) ?><?php //echo $form['is_receive_sms']->renderLabel() ?>
        </div-->

        <?php if (false): ?>
          <div class="pb15 checkboxlist"><?php echo $formRegister['is_legal']->renderLabel() ?></div>
          <?php if ($formRegister['is_legal']->hasError()): ?><div class="pb10 red"><?php echo $formRegister['is_legal']->renderError() ?></div><?php endif ?>
          <!--ul class="checkboxlist pb10">
            <li><label for="radio-3">Для себя как частное лицо</label><input id="radio-3" name="radio-2" type="radio" value="radio-1" /></li>
            <li><label for="radio-4">Для компании как юридическое лицо</label><input id="radio-4" name="radio-2" type="radio" value="radio-2" /></li>
          </ul-->
          <?php echo $formRegister['is_legal']->render() ?>
        <?php endif ?>
      </div>
    </div>

    <div class="line pb20"></div>

    <div class="pl235"><input type="submit" class="button bigbutton" value="Продолжить оформление" /></div>
  </form>


  <div class="popup" id="auth_forgot-block">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="popupbox width345">
      <h2 class="pouptitle">Восстановление пароля</h2>
      <?php include_partial('guard/form_forgot', array('id' => 'auth_forgot-form', 'show_login_link' => false, 'title' => false)) ?>
    </div>
  </div>



<script type="text/javascript">

$(document).ready(function() {

  $('#auth_forgot-link').click(function() {
    $('#auth_forgot-block').lightbox_me({
      centered: true,
      onLoad: function() {
        $('#auth_forgot-form').show()
        $('#auth_forgot-block').find('input:first').focus()
      }
    })

    return false
  })

    var url_signin = '<?php echo url_for('@order_login') ?>',
    url_register = '<?php echo url_for('@order_login') ?>';
    $('#radio-1').click(function(){
      $('#old-user').show();
      $('#old-user input').prop('disabled', null);
      $('#new-user').hide();
      $('#new-user input').prop('disabled', 'disabled');
      $('#form-step-1').prop('action', url_signin);
    });
    $('#radio-2').click(function(){
      $('#old-user').hide();
      $('#old-user input').prop('disabled', 'disabled');
      $('#new-user').show();
      $('#new-user input').prop('disabled', null);
      $('#form-step-1').prop('action', url_register);
    });
    $('#radio-<?php echo ((isset($action) && 'register' == $action) ? 2 : 1) ?>').click();
    $('#form-step-1').submit(function(){
      if (this.action == '') return false;
    });

})
</script>
  <!-- /Form -->
<?php endif ?>

<input type="hidden" disabled="disabled" id="order_login-url" value="<?php echo url_for('@order_login') ?>" />