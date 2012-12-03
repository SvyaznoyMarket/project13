<?php
/** @var $form CorporateRegisterForm */
/** @var $errors array */
?>

<?php slot('title', 'Регистрация юридического лица') ?>

<form action="<?php echo url_for('user_corporateRegister') ?>" method="post">
    <?php render_partial('default/templates/_field_errors.php', array('errors' => !empty($errors['common']) ? $errors['common'] : array())) ?>

    <div class="pb5">Имя:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('first_name'))) ?>
        <input type="text" class="text width315 mb10" name="register[first_name]" value="<?php echo $form->getFirstName() ?>" required="required" />
    </div>

    <div class="pb5">Отчество:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('middle_name'))) ?>
        <input type="text" class="text width315 mb10" name="register[middle_name]" value="<?php echo $form->getMiddleName() ?>" required="required" />
    </div>

    <div class="pb5">Фамилия:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('last_name'))) ?>
        <input type="text" class="text width315 mb10" name="register[last_name]" value="<?php echo $form->getLastName() ?>" required="required" />
    </div>

    <div class="pb5">Контактный e-mail:</div>
    <div class="pb15">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('email'))) ?>
        <input type="email" class="text width315 mb10" name="register[email]" value="<?php echo $form->getEmail() ?>" required="required" /><br/>
        <label class="bSubscibe checked">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe" checked="checked" />
        </label>
    </div>

    <div class="pb5">Мобильный телефон:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('phone'))) ?>
        <input type="text" class="text width315 mb10" name="register[phone]" value="<?php echo $form->getPhone() ?>" required="required" />
        <i>Например, 89081234567</i>
    </div>

    <div class="pb5">Наименование организации:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_name'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_name]" value="<?php echo $form->getCorpName() ?>" required="required" />
    </div>

    <div class="pb5">Юридический адрес:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_legal_address'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_legal_address]" value="<?php echo $form->getCorpLegalAddress() ?>" required="required" />
    </div>

    <div class="pb5">Фактический адрес:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_real_address'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_real_address]" value="<?php echo $form->getCorpRealAddress() ?>" required="required" />
    </div>

    <div class="pb5">ИНН:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_inn'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_inn]" value="<?php echo $form->getCorpINN() ?>" required="required" />
    </div>

    <div class="pb5">КПП:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_kpp'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_kpp]" value="<?php echo $form->getCorpKPP() ?>" required="required" />
    </div>

    <div class="pb5">Расчетный счет:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_account'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_account]" value="<?php echo $form->getCorpAccount() ?>" required="required" />
    </div>

    <div class="pb5">Корреспондентский счет:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_korr_account'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_korr_acount]" value="<?php echo $form->getCorpKorrAccount() ?>" required="required" />
    </div>

    <div class="pb5">БИК:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_bik'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_bik]" value="<?php echo $form->getCorpBIK() ?>" required="required" />
    </div>

    <div class="pb5">Код ОКПО:</div>
    <div class="pb5">
        <?php include_partial('default/field_errors', array('errors' => $form->getError('corp_okpo'))) ?>
        <input type="text" class="text width315 mb10" name="register[corp_okpo]" value="<?php echo $form->getCorpOKPO() ?>" />
    </div>

  <?php if (false): ?>
    <div class="pb5">Код ОКВЭД:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_okved]" />
    </div>

    <div class="pb5">E-mail:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_email]" />
    </div>

    <div class="pb5">Телефон:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_phone]" />
    </div>
  <?php endif ?>

    <input type="submit" tabindex="4" value="Регистрация" class="button bigbutton" />

</form>

<br class="clear" />

<p>&nbsp;</p>