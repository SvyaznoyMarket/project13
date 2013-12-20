<?php
/**
 * @var $page    \View\User\EditPage
 * @var $form    \View\User\EditForm
 * @var $message string
 */
?>

<? if (!$form->isValid()): ?>
    <? foreach ($form->getErrors() as $error): ?>
        <p class="red"><?= $error ?></p>
    <? endforeach ?>
<? elseif ($message): ?>
    <p class="green"><?= $message ?></p>
<? endif ?>

<form class="userInfoEdit clearfix jsUserEditEnterprizeForm" action="<?= $page->url('user.edit') ?>" class="form" method="post">
    <div class="fl width430">
        <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

        <input type="hidden" name="user[enterprize_coupon]" value="<?= $form->getEnterprizeCoupon() ?>">

        <label class="userInfoEdit__label" for="user_first_name">Имя*:</label>
        <div><input type="text" id="user_first_name" value="<?= $form->getFirstName() ?>" name="user[first_name]" class="text width418 mb10 jsFirstName" /></div>

        <label class="userInfoEdit__label" for="user_middle_name">Отчество:</label>
        <div><input type="text" id="user_middle_name" value="<?= $form->getMiddleName() ?>" name="user[middle_name]" class="text width418 mb10 jsMiddleName" /></div>

        <label class="userInfoEdit__label" for="user_last_name">Фамилия*:</label>
        <div><input type="text" id="user_last_name" value="<?= $form->getLastName() ?>" name="user[last_name]" class="text width418 mb10 jsLastName" /></div>

        <label class="userInfoEdit__label" for="user_mobile_phone">Номер телефона*:</label>
        <div><input type="text" id="user_mobile_phone" value="<?= $form->getMobilePhone() ?>" name="user[mobile_phone]" class="text jsMobilePhone" /></div>

        <label class="userInfoEdit__label" for="user_email">E-mail*:</label>
        <div><input type="text" id="user_email" value="<?= $form->getEmail() ?>" name="user[email]" class="text width418 mb10 jsEmail" /></div>

        <label class="userInfoEdit__label" for="user_sclub_card_number">Номер карты Связной-Клуб:</label>
        <div><input type="text" id="user_sclub_card_number" value="<?= $form->getSclubCardnumber() ?>" name="user[svyaznoy_club_card_number]" class="text jsCardNumber" /></div>

        <div style="margin: 0 0 10px 3px;" class="bInputList mRules">
            <input type="checkbox" id="user_agree" name="user[coupon_agree]" value="1" autocomplete="off" class="bCustomInput jsCustomRadio mCustomCheckBig jsAgree" <?= $form->getCouponAgree() ? 'checked="checked"' : '' ?> />
            <label class="bCustomLabel mCustomLabelBig" for="user_agree">Ознакомлен с <a style="text-decoration: underline;" href="http://www.enter.ru/reklamnaya-akcia-enterprize" target="blank">правилами ENTER PRIZE</a>*</label>
        </div>

        <div style="margin: 0 0 10px 3px;" class="bInputList mRules">
            <input type="checkbox" id="user_is_subscribe" name="user[is_subscribe]" value="1" autocomplete="off" class="bCustomInput jsCustomRadio mCustomCheckBig jsSubscribe" <?= $form->getIsSubscribed() ? 'checked="checked"' : '' ?> />
            <label class="bCustomLabel mCustomLabelBig" for="user_is_subscribe">Согласен получать рекламную рассылку</label>
        </div>

        <input type="submit" value="Сохранить изменения" id="bigbutton" class="btnSave button bigbutton jsEnterprizeFormSubmit">
    </div>
</form>
