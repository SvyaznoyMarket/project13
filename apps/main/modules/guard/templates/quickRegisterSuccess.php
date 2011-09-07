<h1>Вход</h1>

<?php if ($photo = $userProfile['photo']) echo image_tag($photo, array('align' => 'left', 'style' => 'padding-right: 10px;')) ?>
Здравствуйте, <?php echo $userProfile ?>.

<br class="clear" />
<p>Для завершения регистрации, пожалуйста, укажите</p>

<div class="block medium">
  <?php echo get_partial('guard/form_quick_register', array('form' => $form)) ?>
</div>