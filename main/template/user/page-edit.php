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

<form class="userInfoEdit clearfix" action="<?= $page->url('user.edit') ?>" class="form" method="post">
    <div class="fl width430">
        <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

        <label class="userInfoEdit__label" for="user_first_name">Имя:</label>

        <input type="text" id="user_first_name" value="<?= $form->getFirstName() ?>" name="user[first_name]" class="text width418 mb10" />

        <label class="userInfoEdit__label" for="user_middle_name">Отчество:</label>

        <input type="text" id="user_middle_name" value="<?= $form->getMiddleName() ?>" name="user[middle_name]" class="text width418 mb10" />

        <label class="userInfoEdit__label" for="user_last_name">Фамилия:</label>

        <input type="text" id="user_last_name" value="<?= $form->getLastName() ?>" name="user[last_name]" class="text width418 mb10" />

        <label class="userInfoEdit__label" for="user_sex">Пол:</label>

        <div class="userInfoEdit__checkBox selectbox170">
            <select id="user_sex" name="user[sex]" >
                <? foreach (array('' => '', '1' => 'мужской', '2' => 'женский') as $sexValue => $sexName): ?>
                    <option value="<?= $sexValue ?>"<? if ((int)$sexValue == (int)$form->getSex()): ?> selected="selected"<? endif ?>><?= $sexName ?></option>
                <? endforeach ?>
            </select>
        </div>

        <label class="userInfoEdit__label" for="user_email">E-mail:</label>

        <input type="text" id="user_email" value="<?= $form->getEmail() ?>" name="user[email]" class="text width418 mb10" <? if ($form->getIsDisabled()): ?>disabled="disabled"<? endif ?> />

        <div class="pr fr">
            <div class="doublehelp help">Одно из полей обязательно для заполнения!</div>
        </div>

        <label class="userInfoEdit__label" for="user_mobile_phone">Мобильный телефон:</label>

        <input type="text" id="user_mobile_phone" value="<?= $form->getMobilePhone() ?>" name="user[mobile_phone]" class="text" <? if ($form->getIsDisabled()): ?>disabled="disabled"<? endif ?> />

        <label class="userInfoEdit__label" for="user_home_phone">Домашний телефон:</label>

        <input type="text" id="user_home_phone" value="<?= $form->getHomePhone() ?>" name="user[home_phone]" class="text" />

        <label class="userInfoEdit__label">Дата рождения:</label>

        <div class="userInfoEdit__checkBox clearfix">
            <div class="checkBox selectbox75 fl">
                <? $selectedDay = $form->getBirthday() ? $form->getBirthday()->format('j') : '' ?>
                <select id="user_birthday_day" name="user[birthday][day]">
                    <? foreach (array_merge(array(''), range(1, 31)) as $day):  ?>
                        <option value="<?= $day ?>"<? if ((int)$day == (int)$selectedDay): ?> selected="selected"<? endif ?>><?= $day ?></option>
                    <? endforeach ?>
                </select>
            </div>

            <div class="checkBox selectbox98 fl">
                <? $selectedMonth = $form->getBirthday() ? $form->getBirthday()->format('n') : '' ?>
                <select id="user_birthday_month" name="user[birthday][month]">
                    <? foreach (array_merge(array(''), range(1, 12)) as $month): ?>
                        <option value="<?= $month ?>"<? if ((int)$month == (int)$selectedMonth): ?> selected="selected"<? endif ?>><?= $month ?></option>
                    <? endforeach ?>
                </select>
            </div>

            <div class="checkBox selectbox75 fl">
                <? $selectedYear = $form->getBirthday() ? $form->getBirthday()->format('Y') : '' ?>
                <select id="user_birthday_year" name="user[birthday][year]">
                    <? foreach (array_merge(array(''), range(2005, 1930)) as $year): ?>
                        <option value="<?= $year ?>"<? if ((int)$year == (int)$selectedYear): ?> selected="selected"<? endif ?>><?= $year ?></option>
                    <? endforeach ?>
                </select>
            </div>
        </div>

        <? // TODO (SITE-3792) Ядерный метод больше не работает с полем "svyaznoy_club_card_number" появилось поле "bonus_card", поэтому пока что поле закомменчено
        /*
        <label class="userInfoEdit__label" for="user_sclub_card_number">Номер карты Связной-Клуб:</label>
        <div><input type="text" id="user_sclub_card_number" value="<?= $form->getSclubCardnumber() ?>" name="user[svyaznoy_club_card_number]" class="text jsCardNumber" /></div>
        */ ?>

        <label class="userInfoEdit__label" for="user_occupation">Род деятельности:</label>

        <input type="text" id="user_occupation" value="<?= $form->getOccupation() ?>" name="user[occupation]" class="text width418 mb10" />

        <input type="submit" value="Сохранить изменения" id="bigbutton" class="btnSave button bigbutton">

    </div>
</form>
