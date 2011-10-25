<?php echo slot('title', 'Вход') ?>

<?php if ($photo = $userProfile['photo']) echo image_tag($photo, array('align' => 'left', 'style' => 'padding-right: 10px;')) ?>
Здравствуйте, <?php echo $userProfile ?>.

<br />
<p>Для завершения регистрации, пожалуйста, укажите</p>

<?php echo get_partial('guard/form_quick_register', array('form' => $form)) ?>
