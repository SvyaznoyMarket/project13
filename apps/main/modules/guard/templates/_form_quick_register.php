<form class="form" action="<?php echo url_for('@user_quickRegister') ?>" method="post">

  <div class="pb5">Email:</div>
  <div class="pb5">
    <?php echo $form['username']->renderError() ?>
    <?php echo $form['username']->render(array('class' => 'text width315 mb10')) ?>
  </div>

  <input type="submit" value="Готово!" class="button bigbutton" />
</form>