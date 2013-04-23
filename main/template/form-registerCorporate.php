<?php
/** @var $page \View\User\CorporateRegistrationPage */
/** @var $form \View\User\CorporateRegistrationForm */
?>

<form action="<?= $page->url('user.registerCorporate') ?>" method="post">

    <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

    <div class="pb5">Имя:</div>
    <div class="pb5">
        <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[first_name]" value="<?= $form->getFirstName() ?>" />
    </div>

    <div class="pb5">Отчество:</div>
    <div class="pb5">
        <? if ($error = $form->getError('middle_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[middle_name]" value="<?= $form->getMiddleName() ?>" />
    </div>

    <div class="pb5">Фамилия:</div>
    <div class="pb5">
        <? if ($error = $form->getError('last_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[last_name]" value="<?= $form->getLastName() ?>" />
    </div>

    <div class="pb5">Контактный e-mail:</div>
    <div class="pb15">
        <? if ($error = $form->getError('email')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[email]" value="<?= $form->getEmail() ?>" /><br />
        <label class="bSubscibe checked">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe" checked="checked" />
        </label>
    </div>

    <div class="pb5">Мобильный телефон:</div>
    <div class="pb5">
        <? if ($error = $form->getError('phone')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[phone]" value="<?= $form->getPhone() ?>" maxlength="11" />
        <i>Например, 89081234567</i>
    </div>

    <div class="pb5">Организационно правовая форма:</div>
    
    <div class="pb5">
        <select id="corp_select" name="register[corp_form]" class="text width315 mb10">
        <? foreach ($form->getCorpFormSelection() as $value => $name): ?>
            <option value="<?= $value ?>"<? if ($value == $form->getCorpForm()): ?> selected="selected" <? endif ?>><?= $name ?></option>
        <? endforeach ?>
            <option value="Другая форма">Другая форма</option>
        </select>
    </div>

    <div class="pb5">Наименование организации:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_name]" value="<?= $form->getCorpName() ?>" />
    </div>

    <div class="pb5">Юридический адрес:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_legal_address')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_legal_address]" value="<?= $form->getCorpLegalAddress() ?>" />
    </div>

    <div class="pb5">Фактический адрес:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_real_address')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_real_address]" value="<?= $form->getCorpRealAddress() ?>" />
    </div>

    <div class="pb5">ИНН:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_inn')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_inn]" value="<?= $form->getCorpINN() ?>" />
    </div>

    <div class="pb5">КПП:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_kpp')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_kpp]" value="<?= $form->getCorpKPP() ?>" />
    </div>

    <div class="pb5">Расчетный счет:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_account')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_account]" value="<?= $form->getCorpAccount() ?>" />
    </div>

    <div class="pb5">Корреспондентский счет:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_korr_account')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_korr_acount]" value="<?= $form->getCorpKorrAccount() ?>" />
    </div>

    <div class="pb5">БИК:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_bik')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_bik]" value="<?= $form->getCorpBIK() ?>" />
    </div>

    <div class="pb5">Код ОКПО:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_okpo')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_okpo]" value="<?= $form->getCorpOKPO() ?>" />
    </div>

    <? if (false): ?>
    <div class="pb5">Код ОКВЭД:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_okved')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_okved]" />
    </div>

    <div class="pb5">E-mail:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_email')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_email]" />
    </div>

    <div class="pb5">Телефон:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_phone')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_phone]" />
    </div>
    <? endif ?>

    <input type="submit" tabindex="4" value="Регистрация" class="button bigbutton" />

</form>
<div id="corpNotice" class="popup width315">
    <a class="close" href="#">Закрыть</a>
    <p class="font16">Для дальнейшей регистрации на нашем сайте просим выслать карточку основных сведений организации по адресу <a href="mailto:partner@enter.ru">partner@enter.ru</a></p>
    <p class="font16">Мы свяжемся с вами в течение 10 минут.</p>
</div>
