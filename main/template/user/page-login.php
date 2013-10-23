<?php
/**
 * @var $page     \View\User\LoginPage
 * @var $form     \View\User\LoginForm|\View\User\RegistrationForm|null
 */
?>

<div class="bPageLogin clearfix">
    <div class="bPageLogin_eLogo fl"><a href="/"></a></div>
    <div class="bPageLogin_eContent bLoginPageForms fl">
        <h1 class="bLoginPageForms_eTitle">хотите быть в курсе новостей от enter?</h1>
        <div class="clear"></div>
        <p class="bLoginPageForms_eDesc">Зарегистрируйтесь и получайте актуальную информацию<br/>
            о новых поступлениях, акциях и распродажах!</p>

        <?= $page->render('form-register') ?>
        <div class="clear"></div>
        <a class="font18 dashed jsHideLoginform" href="#">У меня уже есть логин и пароль</a>
        <div class="clearfix"><?= $page->render('form-forgot') ?></div>
        <?= $page->render('form-login') ?>
    </div>
</div>