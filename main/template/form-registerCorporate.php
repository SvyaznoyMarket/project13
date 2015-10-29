<?php
/**
 * @var $page           \View\User\CorporateRegistrationPage
 * @var $form           \View\User\CorporateRegistrationForm
 */
?>

<div class="bBusinessReg">

    <?= $content ?> <!-- часть странице в вордпрессе reg_corp_user_cont -->

    <? if (\App::config()->user['corporateRegister']): ?>
        <h2 class="bTitle" id="bCorpRegFormHead">Регистрация юридического лица</h2>
        <form class="bCorpRegForm" action="<?= $page->url('user.registerCorporate') ?>" method="post">

            <? if ($error = $form->getError('global')) echo $page->render('_formError', ['error' => $error]) ?>

            <label class="bCorpRegForm__eLabel">Фамилия:</label>
            <input type="text" class="bCorpRegForm__eInput" name="register[last_name]" value="<?= $form->getLastName() ?>" />
            <? if ($error = $form->getError('last_name')) echo $page->render('_formError', ['error' => $error]) ?>

            <label class="bCorpRegForm__eLabel">Имя:</label>
            <input type="text" class="bCorpRegForm__eInput" name="register[first_name]" value="<?= $form->getFirstName() ?>" />
            <? if ($error = $form->getError('first_name')) echo $page->render('_formError', ['error' => $error]) ?>

            <label class="bCorpRegForm__eLabel">Отчество:</label>
            <input type="text" class="bCorpRegForm__eInput" name="register[middle_name]" value="<?= $form->getMiddleName() ?>" />
            <? if ($error = $form->getError('middle_name')) echo $page->render('_formError', ['error' => $error]) ?>

            <label class="bCorpRegForm__eLabel">E-mail:</label>
            <input type="text" class="bCorpRegForm__eInput" name="register[email]" value="<?= $form->getEmail() ?>" />
            <? if ($error = $form->getError('email')) echo $page->render('_formError', ['error' => $error]) ?>

            <div class="bInputList">
                <input type="checkbox" id="subscribeCheck" name="subscribe" value="1" autocomplete="off" class="bCustomInput mCustomCheckbox" checked="checked" />
                <label class="bCustomLabel" for="subscribeCheck">Хочу знать об интересных предложениях</label>
            </div>

            <label class="bCorpRegForm__eLabel">Мобильный телефон:</label>
            <input type="text" class="bCorpRegForm__eInput" name="register[phone]" value="<?= $form->getPhone() ?>" maxlength="11" placeholder="89ХХХХХХХХХ" />
            <? if ($error = $form->getError('phone')) echo $page->render('_formError', ['error' => $error]) ?>

            <div class="bCompanyDataSection">
                <label class="bCorpRegForm__eLabel m2Line">Организационно правовая форма:</label>
                <select id="corp_select" name="register[corp_form]" class="bCorpRegForm__eSelect">
                <? foreach ($form->getCorpFormSelection() as $value => $name): ?>
                    <option value="<?= $value ?>"<? if ($value == $form->getCorpForm()): ?> selected="selected" <? endif ?>><?= $name ?></option>
                <? endforeach ?>
                    <option value="Другая форма">Другая форма</option>
                </select>

                <label class="bCorpRegForm__eLabel m2Line">Наименование организации:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_name]" value="<?= $form->getCorpName() ?>" />
                <? if ($error = $form->getError('corp_name')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">Юридический адрес:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_legal_address]" value="<?= $form->getCorpLegalAddress() ?>" />
                <? if ($error = $form->getError('corp_legal_address')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">Фактический адрес:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_real_address]" value="<?= $form->getCorpRealAddress() ?>" />
                <? if ($error = $form->getError('corp_real_address')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">ИНН:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_inn]" value="<?= $form->getCorpINN() ?>" />
                <? if ($error = $form->getError('corp_inn')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">КПП:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_kpp]" value="<?= $form->getCorpKPP() ?>" />
                <? if ($error = $form->getError('corp_kpp')) echo $page->render('_formError', ['error' => $error]) ?>


                <label class="bCorpRegForm__eLabel">Расчетный счет:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_account]" value="<?= $form->getCorpAccount() ?>" />
                <? if ($error = $form->getError('corp_account')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel m2Line">Корреспондентский счет:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_korr_acount]" value="<?= $form->getCorpKorrAccount() ?>" />
                <? if ($error = $form->getError('corp_korr_account')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">БИК:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_bik]" value="<?= $form->getCorpBIK() ?>" />
                <? if ($error = $form->getError('corp_bik')) echo $page->render('_formError', ['error' => $error]) ?>

                <label class="bCorpRegForm__eLabel">Код ОКПО:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[corp_okpo]" value="<?= $form->getCorpOKPO() ?>" />
                <? if ($error = $form->getError('corp_okpo')) echo $page->render('_formError', ['error' => $error]) ?>

                <? if (false): ?>
                    <label class="bCorpRegForm__eLabel">Код ОКВЭД:</label>
                    <? //if ($error = $form->getError('corp_okved')) echo $page->render('_formError', ['error' => $error]) ?>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_okved]" />

                    <label class="bCorpRegForm__eLabel">E-mail:</label>
                    <? //if ($error = $form->getError('corp_email')) echo $page->render('_formError', ['error' => $error]) ?>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_email]" />

                    <label class="bCorpRegForm__eLabel">Телефон:</label>
                    <? //if ($error = $form->getError('corp_phone')) echo $page->render('_formError', ['error' => $error]) ?>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_phone]" />
                <? endif ?>
            </div>

        <input type="submit" tabindex="4" value="Регистрация" class="button bigbutton" />

        </form>

        <div id="corpNotice" class="popup width315">
            <a class="close" href="#">Закрыть</a>
            <p class="font16">Для дальнейшей регистрации на нашем сайте просим выслать карточку основных сведений организации по адресу <a href="mailto:partner@enter.ru">partner@enter.ru</a></p>
            <p class="font16">Мы свяжемся с вами в течение 10 минут.</p>
        </div>

    <? endif ?>
</div>