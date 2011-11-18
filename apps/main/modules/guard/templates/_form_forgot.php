<?php $id = isset($id) ? $id : 'reset-pwd-form' ?>
<?php $show_login_link = isset($show_login_link) ? $show_login_link : true ?>
<?php $title = isset($title) ? $title : 'Восстановление пароля:' ?>

<form id="<?php echo $id ?>" style="display: none;" action="<?php echo url_for('@user_forgotPassword') ?>" class="form" method="post">
	<div class="fl width327 mr20">

    <?php if ($title): ?>
    <div class="font16 pb20"><?php echo $title ?></div>
    <?php endif ?>

		<div class="pb5">Введите e-mail или мобильный телефон, который использовали при регистрации, и мы пришлем вам пароль.</div>
		<div class="error_list"></div>
		<div class="pb5"><input name="login" type="text" class="text width315 mb10" value="" /></div>
		<input type="submit" class="fr button whitebutton" value="Отправить запрос" />
		<div class="clear pb10"></div>

    <?php if ($show_login_link): ?>
      Если вы вспомнили пароль, то вам надо лишь<br /><strong><a id="remember-pwd-trigger" href="javascript:void(0)" class="orange underline">войти в систему</a></strong>.
    <?php endif ?>

	</div>
</form>