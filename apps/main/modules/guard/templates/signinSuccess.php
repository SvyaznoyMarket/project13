<?php //slot('form_signin', ' ') ?>

<?php slot('title', 'Авторизация') ?>

<?php include_component('guard', 'form_auth', array('formSignin' => $form)) ?>

<br />