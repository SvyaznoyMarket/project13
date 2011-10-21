  <form id="reset-pwd-key-form" style="" action="<?php echo url_for('@user_resetPassword') ?>" class="form" method="post">
	<div class="fl width327 mr20">
		<div class="font16 pb20">Восстановление пароля:</div>
		<div class="pb5">Введите ключ, который был вам выслан по почте или смс.</div>
		<div class="error_list"></div>
		<div class="pb5"><input name="token" type="text" class="text width315 mb10" value="" /></div>
		<input type="submit" class="fr button whitebutton" value="Отправить запрос" />
		<div class="clear pb10"></div>
		Если вы вспомнили пароль, то вам надо лишь<br /><strong><a id="remember-pwd-trigger2" href="javascript:void(0)" class="orange underline">войти в систему</a></strong>.
	</div>
  </form>