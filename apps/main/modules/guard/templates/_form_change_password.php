<form action="<?php echo url_for('@user_changePassword') ?>" method="post">
    <div class="fl width430">
		
<!--        <div class="pb20"><strong>Чтобы изменить пароль, укажите свой текущий пароль</strong></div>-->
		
		<?php echo $form->renderHiddenFields() ?>
		
		<div class="pr fr"><div class="help">
			<br/><br/><br/>
			Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.
		</div></div>

		<div class="pb10">
			<?php echo $form['password']->renderLabel() ?>:
			<?php echo $form['password']->renderError() ?>
		</div>
		<?php echo $form['password']->render() ?>

		<div class="pb10">
			<?php echo $form['password_again']->renderLabel() ?>:
			<?php echo $form['password_again']->renderError() ?>
		</div>
		<?php echo $form['password_again']->render() ?>

		<div class="clear pb10"></div>
		<div class="pb20"><input type="submit" class="button yellowbutton" id="bigbutton" value="Сохранить изменения" /></div>

		<div class="attention font11">Внимание! После смены пароля Вам придет письмо (SMS) с новым паролем</div>

    </div>
</form>
<?php echo $form->renderGlobalErrors(); // var_dump()?>