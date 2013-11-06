<?php
/**
 * @var $page     \View\Layout
 * @var $request  \Http\Request
 */
?>

<?php
if (!isset($form)) $form = new \View\User\RegistrationForm();
?>

<form action="<?= $page->url('user.register') ?>" class="form bFormLogin__ePlace jsRegisterForm" style="margin-right: 0;" method="post">
    <fieldset class="bFormLogin__ePlace">
        <legend class="bFormLogin__ePlaceTitle">Я новый пользователь</legend>

        <label class="bFormLogin__eLabel">Ваше имя:</label>
        <div><input type="text" class="text bFormLogin__eInput jsRegisterFirstName" name="register[first_name]" value="<?= $form->getFirstName() ?>"/></div>

        <label class="bFormLogin__eLabel registerAnotherWay">Ваш e-mail:</label>
        <a class="bFormLogin__eLinkHint eMail registerAnotherWayBtn" href="#">У меня нет e-mail</a>

        <div><input type="text" class="text bFormLogin__eInput jsRegisterUsername" name="register[username]" value="<?= $form->getUsername() ?>" /></div>

        <div class="bInputList">
            <input class="jsCustomRadio bCustomInput mCustomCheckBig" name="subscribe" id="subscribe" type="checkbox" checked="checked" hidden />
            <label class="bCustomLabel mCustomLabelBig" style="/*display: block;*/" for="subscribe">Хочу знать об интересных<br/>предложениях</label>
        </div>

        <input type="submit" class="bigbutton bFormLogin__eBtnSubmit jsSubmit" data-loading-value="Регистрируюсь..." value="Регистрация" />

        <p class="bRulesText">Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="/terms">Условиями продажи...</a></p>

        <? if (\App::config()->user['corporateRegister']): ?>
            <div class="bAuthCompany"><a href="<?= $page->url('user.registerCorporate') ?>">Регистрация юридического лица</a></div>
        <? endif ?>
    </fieldset>
</form>