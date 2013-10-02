<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Registration -->
<? /* <div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>
	<div class="pouptitle">Вход в Enter</div>

    <div class="registerbox clearfix">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
        <?= $page->render('form-register') ?>
    </div>
</div>
<!-- /Registration --> 
*/?>

<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>

    <div class="bPopupTitle">ВХОД В ENTER</div>

    <div class="bFormLogin">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
        <?= $page->render('form-register') ?>
    </div>

    <!--<form class="bFormLogin">
        <fieldset class="bFormLogin__ePlace">
            <legend class="bFormLogin__ePlaceTitle">У меня есть логин и пароль</legend>

            <label class="bFormLogin__eLabel">E-mail или мобильный телефон:</label>
            <input class="text bFormLogin__eInput" type="text" />

            <label class="bFormLogin__eLabel">Пароль:</label>
            <a class="bFormLogin__eLinkHint mForgotPassword" href="">Забыли пароль?</a>
            <input class="text bFormLogin__eInput" type="text" />

            <button class="bigbutton bFormLogin__eBtnSubmit">Войти</button>
        </fieldset>

        <fieldset class="bFormLogin__ePlace">
            <legend class="bFormLogin__ePlaceTitle">Я новый пользователь</legend>

            <label class="bFormLogin__eLabel">Ваше имя:</label>
            <input class="text bFormLogin__eInput" type="text" />

            <label class="bFormLogin__eLabel">Ваш e-mail:</label>
            <a class="bFormLogin__eLinkHint eMail" href="">У меня нет e-mail</a>
            <input class="text bFormLogin__eInput" type="text" />

            <div class="bInputList">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig" name="subscribe" id="subscribe" type="checkbox" hidden />
                <label class="bCustomLabel mCustomLabelBig" for="subscribe">Хочу знать об интересных<br/>предложениях</label>
            </div>

            <button class="bigbutton bFormLogin__eBtnSubmit mDisabled">Регистрация</button>

            <p class="bRulesText">Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="">Условиями продажи...</a></p>

            <div class="bAuthCompany"><a href="">Регистрация юридического лица</a></div>
        </fieldset>
    </form>-->
</div>