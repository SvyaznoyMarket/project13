<?php
/**
 * @var $page \View\User\LoginPage
 * @var $form \View\User\LoginForm|\View\User\RegistrationForm|null
 */
?>

<div class="bPageLogin clearfix">
	<div class="bPageLogin_eLogo fl"></div>
	<div class="bPageLogin_eContent bLoginPageForms fl">
		<h1 class="bLoginPageForms_eTitle">хотите быть в курсе новостей от enter?</h1>
		<div class="clear"></div>
		<p class="bLoginPageForms_eDesc">Зарегистрируйтесь и получайте актуальную информацию<br/>
			о новых поступлениях, акциях и распродажах!</p>
		
		<form method="post" class="form" action="<?= $page->url('user.register') ?>" id="register-form">
			<input type="hidden" value="<?= $page->url('user.login') ?>" name="redirect_to">

			<div class="bLoginForm width327 fl">
				<h2 class="bLoginForm_eTitle">Я новый пользователь</h2>
                <? if (\App::config()->user['corporateRegister']): ?>
                    <a class="bLoginForm_eUr orange underline" href="<?= $page->url('user.registerCorporate') ?>">Регистрация юридического лица</a>
                <? endif ?>

				<div class="pb5">Как к вам обращаться?</div>
				<div class="pb5">
					<input type="text" tabindex="5" value="" name="register[first_name]" class="text width315 mb10" id="register_first_name" required="required">
				</div>
				<div class="pb5 clearfix">
					<span class="registerAnotherWay">Ваш e-mail</span>:<a href="#" class="registerAnotherWayBtn font10 fr">У меня нет e-mail</a>
				</div>
				<div class="pb5">
					<span class="registerPhonePH">+7</span>
					<input type="text" tabindex="6" value="" name="register[username]" class="text width315 mb10" id="register_username" required="required">
				</div>
				<label class="bSubscibe fl checked" style="display: inline;">
					<b></b> Хочу знать об интересных<br>предложениях
					<input type="checkbox" checked="checked" class="subscibe hiddenCheckbox" autocomplete="off" value="1" name="subscribe">
				</label>
				<input type="submit" tabindex="10" value="Регистрация" class="fr button bigbutton mDisabled">
				<div class="clear"></div>
				<p>Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="/terms" target="_blank" class="underline">Условиями продажи...</a></p>
			</div>
			<img class="fl mr20" src="http://content.enter.ru/wp-content/uploads/2013/02/loginFormImg.jpg"/>
		</form>
		<div class="clear"></div>
		<a id="hideLoginform" class="font18 dashed" href="#">У меня уже есть логин и пароль</a>
		<div class="clearfix"><?= $page->render('form-forgot') ?></div>
		<form id="login-form" method="post" class="form hf" action="<?= $page->url('user.login') ?>" id="login-form">
			<input type="hidden" value="<?= $page->url('user.login') ?>" name="redirect_to">
			<div class="width327 bLoginForm clearfix">
				<h2 class="bLoginForm_eTitle">У меня есть логин и пароль</h2>
				<div class="pb5">E-mail или мобильный телефон:</div>
				<div class="pb5">
					<input type="text" name="signin[username]" value="" tabindex="1" class="text width315 mb10" id="signin_username" required="required" />
				</div>
				<div class="pb5">
					<a class="fr orange underline" href="<?= $page->url('user.forgot') ?>" id="forgot-pwd-trigger">Забыли пароль?</a>
					Пароль:
				</div>
				<div class="pb5">
					<input type="password" tabindex="2" name="signin[password]" class="text width315 mb10" id="signin_password" required="required" />
				</div>
				<input type="submit" tabindex="3" value="Войти" class="fr button bigbutton">
			</div>
		</form>

	</div>
</div>