<form action="<?php echo url_for('@welcome') ?>" method="post">
  <input type="text" class="text" value="Введи секретное слово :)"  onfocus="if (this.value == 'Введи секретное слово :)') this.value = '';" onblur="if (this.value == '') this.value = 'Введи секретное слово :)';" name="<?php echo sfConfig::get('app_welcome_cookie_name') ?>" />
  <input type="submit" class="entrybutton" value="Войти" />
  <input type="hidden" name="url" value="<?php echo $url ?>" />
</form>