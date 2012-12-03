<?php
/**
 * @var $page \View\User\LoginPage
 * @var $form \View\User\LoginForm|\View\User\RegistrationForm|null
 */
?>

<div class="registerbox">

    <?= $page->render('form-forgot') ?>

    <?= $page->render('form-login', array('form' => $form instanceof \View\User\LoginForm ? $form : null)) ?>

    <?= $page->render('form-register', array('form' => $form instanceof \View\User\RegistrationForm ? $form : null)) ?>

    <div class="clear"></div>
</div>


<br />