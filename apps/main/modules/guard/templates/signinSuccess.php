<?php //slot('form_signin', ' ') ?>

<?php slot('title', 'Авторизация') ?>

<div class="block medium">
  <?php include_component('guard', 'form_auth', array('formSignin' => $form)) ?>
</div>