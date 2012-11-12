<?php
/**
 * @var $page \View\User\LoginPage
 * @var $form \View\User\LoginForm
 */
?>

<div class="registerbox">

    <?= $page->render('form-forgot') ?>

    <?= $page->render('form-login', array('form' => $form)) ?>

    <?= $page->render('form-register') ?>

    <div class="clear"></div>
</div>


<br />