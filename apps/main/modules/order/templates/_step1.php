<?php slot('receipt') ?>
  <?php include_component('order', 'receipt') ?>
<?php end_slot() ?>

<?php slot('step') ?>
        <ul class="steplist steplist2">
            <li><a href="<?php echo url_for('order_new', array('step' => 1,)) ?>"><span>Шаг 1</span>Данные<br />покупателя</a></li>
            <li><span>Шаг 2</span>Способ доставки<br />и оплаты</li>
            <li><span>Шаг 3</span>Подтверждение<br />заказа</li>
        </ul>
<?php end_slot() ?>

<?php if ($sf_user->isAuthenticated()): ?>
<h3>Вы уже вошли на сайт</h3>
<a href="<?php echo url_for('order_new', array('step'=>2)) ?>">Продолжить</a>
<?php else: ?>
    <!-- Form -->
    <form action="" method="post" class="form" id="form-step-1">
		<input type="hidden" name="redirect_to" value="<?php echo url_for('order_new', array('step'=>2)) ?>"/>
        <div class="fl width215 mr20">
            <div class="pb40"><strong class="font16">Данные покупателя:</strong></div>
            <div class="gray pb10">Заполнить данные, используя</div>
            <ul class="backetsharelist">
                <li><a href="" class="facebook">Facebook</a></li>
                <li><a href="" class="vkontakte">Вконтакте</a></li>
                <li><a href="" class="mailru">Mail.ru</a></li>
                <li><a href="" class="odnoklassniki">Одноклассники</a></li>
                <li><a href="" class="twitter">Twitter</a></li>
            </ul>
        </div>

        <div class="fl width430">

            <ul class="checkboxlist pb10">
                <li class="font16"><label for="radio-1">Уже покупали у нас?</label><input id="radio-1" name="radio-1" type="radio" value="radio-1" /></li>
                <li class="font16"><label for="radio-2">Я покупаю впервые!</label><input id="radio-2" name="radio-1" type="radio" value="radio-2"/></li>
            </ul>

			<div id="old-user" style="display:none;">
				<div class="pb10">E-mail или мобильный телефон:</div>
				<?php echo $formSignin['username']->render(array('class' => 'text width418 mb15')) ?>

				<div class="pb5"><a href="<?php echo url_for('user_forgotPassword') ?>	" class="fr orange underline">Забыли пароль?</a>Пароль:</div>
				<?php echo $formSignin['password']->render(array('class' => 'text width418 mb15')) ?>

				<?php echo $formSignin['remember']->render(array('class' => 'hiddenCheckbox', 'id' => 'checkbox-8')) ?>
			</div>

			<div id="new-user" style="display:none;">
				<div class="pb10">E-mail или мобильный телефон:</div>
				<input name="username" type="text" class="text width418 mb15"/>

				<div class="pb10">Как к вам обращаться?</div>
				<input name="first_name" type="text" class="text width418 mb5"/>
				<div class="font11 pb20">Эти данные необходимы для регистрации в системе и оформления платежа. Вам будет выслан пароль на ваш e-mail или моб. телефон</div>


				<div class="pb15">Вы покупаете:</div>
				<ul class="checkboxlist pb10">
					<li><label for="radio-3">Для себя как частное лицо</label><input id="radio-3" name="radio-2" type="radio" value="radio-1" /></li>
					<li><label for="radio-4">Для компании как юридическое лицо</label><input id="radio-4" name="radio-2" type="radio" value="radio-2" /></li>
				</ul>
			</div>
        </div>

        <div class="line pb20"></div>
        <div class="pl235"><input type="submit" class="button bigbutton" value="Продолжить оформление" /></div>
    </form>
	<script type="text/javascript">
		var url_signin = '<?php echo url_for('@user_signin') ?>',
			url_register = '<?php echo url_for('@user_register') ?>';
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
		$('#radio-1').click();
		$('#form-step-1').submit(function(){
			if (this.action == '') return false;
		});
	</script>
    <!-- /Form -->
<?php endif ?>