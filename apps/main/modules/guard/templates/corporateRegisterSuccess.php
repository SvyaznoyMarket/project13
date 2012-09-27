<?php slot('title', 'Регистрация юридического лица') ?>

<form action="<?php echo url_for('user_corporateRegister') ?>" method="post">


    <div class="pb5">Имя:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[first_name]">
    </div>

    <div class="pb5">Отчество:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[middle_name]">
    </div>

    <div class="pb5">Фамилия:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[last_name]">
    </div>

    <div class="pb5">Контактный e-mail:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[email]">
    </div>

    <div class="pb5">Мобильный телефон:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[phone]">
    </div>

    <div class="pb5">Наименование организации:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_name]">
    </div>

    <div class="pb5">Юридический адрес:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_legal_address]">
    </div>

    <div class="pb5">Фактический адрес:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_real_address]">
    </div>

    <div class="pb5">ИНН:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_inn]">
    </div>

    <div class="pb5">КПП:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_kpp]">
    </div>

    <div class="pb5">Расчетный счет:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_acount]">
    </div>

    <div class="pb5">Корреспондентский счет:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_korr_acount]">
    </div>

    <div class="pb5">БИК:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_bik]">
    </div>

    <div class="pb5">Город:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_city]">
    </div>

    <div class="pb5">Код ОКПО:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_okpo]">
    </div>

    <div class="pb5">Код ОКВЭД:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_okved]">
    </div>

    <div class="pb5">E-mail:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_email]">
    </div>

    <div class="pb5">Телефон:</div>
    <div class="pb5">
        <input type="text" class="text width315 mb10" name="register[corp_phone]">
    </div>

</form>