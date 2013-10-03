<?php
/**
 * @var $page     \View\Layout
 * @var $request  \Http\Request
 * @var $redirect string
 */
?>

<?php
if (empty($redirect)) $redirect = $request->getRequestUri();
if (!isset($form)) $form = new \View\User\RegistrationForm();
?>

<?/*?>
<form id="register-form" action="<?= $page->url('user.register') ?>" class="form" method="post">
    <input type="hidden" name="redirect_to" value="<?= $redirect ?>"/>

    <div class="fr width327 ml20">
        <div class="font16 pb20">Я новый пользователь</div>

        <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

        <div class="pb5">Ваше имя:</div>
        <div class="pb5">
            <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>
            <input type="text" id="register_first_name" class="text width315 mb10" name="register[first_name]" value="<?= $form->getFirstName() ?>" tabindex="5"/>
        </div>
        <div class="pb5 clearfix"><span class="registerAnotherWay">Ваш e-mail</span>:<a class="registerAnotherWayBtn font10 fr" href="#">У меня нет e-mail</a></div>
        <div class="pb5">
            <? if ($error = $form->getError('username')) echo $page->render('_formError', array('error' => $error)) ?>
            <span class="registerPhonePH">+7</span>
            <input type="text" id="register_username" class="text width315 mb10" name="register[username]" value="<?= $form->getUsername() ?>" tabindex="6"/>

        </div>
        <label class="bSubscibe fl checked">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe" checked="checked" />

        </label>
        <input type="submit" class="fr button bigbutton mDisabled" value="Регистрация" tabindex="10"/>
        <div class="clear"></div>
        <p>Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a class="underline" href="/terms">Условиями продажи...</a></p>
        <? if (\App::config()->user['corporateRegister']): ?>
        <p><a href="<?= $page->url('user.registerCorporate') ?>" class="orange underline">Регистрация юридического лица</a></p>
        <? endif ?>

    </div>
</form>
<?*/?>

<form id="register-form" action="<?= $page->url('user.register') ?>" class="form bFormLogin__ePlace" method="post">
    <input type="hidden" name="redirect_to" value="<?= $redirect ?>"/>

    <legend class="bFormLogin__ePlaceTitle">Я новый пользователь</legend>

    <label class="bFormLogin__eLabel">Ваше имя:</label>
    <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>
    <input type="text" id="register_first_name" class="text bFormLogin__eInput" name="register[first_name]" value="<?= $form->getFirstName() ?>"/>

    <label class="bFormLogin__eLabel registerAnotherWay">Ваш e-mail:</label>
    <a class="bFormLogin__eLinkHint eMail registerAnotherWayBtn" href="#">У меня нет e-mail</a>

    <? if ($error = $form->getError('username')) echo $page->render('_formError', array('error' => $error)) ?>
    <div class="pb5">
        <span class="registerPhonePH">+7</span>
        <input type="text" id="register_username" class="text bFormLogin__eInput" name="register[username]" value="<?= $form->getUsername() ?>" />
    </div>

    <div class="bInputList">
        <input class="jsCustomRadio bCustomInput mCustomCheckBig" name="subscribe" id="subscribe" type="checkbox" checked="checked" hidden />
        <label class="bCustomLabel mCustomLabelBig" style="/*display: block;*/" for="subscribe">Хочу знать об интересных<br/>предложениях</label>
    </div>

    <input type="submit" class="bigbutton bFormLogin__eBtnSubmit" value="Регистрация" />

    <p class="bRulesText">Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="/terms">Условиями продажи...</a></p>

    <? if (\App::config()->user['corporateRegister']): ?>
        <div class="bAuthCompany"><a href="<?= $page->url('user.registerCorporate') ?>">Регистрация юридического лица</a></div>
    <? endif ?>
</form>