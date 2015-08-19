<?php
/**
 * @var $page       \View\User\EditPage
 * @var $form       \View\User\EditForm
 * @var $flash      array|null
 * @var $bonusCards array
 */
?>

<div class="personalPage">

    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div class="personalTitle">Изменить мои данные</div>

    <? if ($flash !== null) : ?>
        <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
    <? endif ?>

    <form action="<?= $page->url('user.edit') ?>" method="post" class="personalData">
        <fieldset class="personalData_left">
            <legend class="legend">Личные данные</legend>

            <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

            <label class="personalData_label labeltext">Имя:</label>
            <input class="personalData_text textfield" type="text" value="<?= $form->getFirstName() ?>" name="user[first_name]" />

            <label class="personalData_label labeltext">Отчество:</label>
            <input class="personalData_text textfield" type="text" value="<?= $form->getMiddleName() ?>" name="user[middle_name]" />

            <label class="personalData_label labeltext">Фамилия:</label>
            <input class="personalData_text textfield" type="text" value="<?= $form->getLastName() ?>" name="user[last_name]" />

            <div class="personalData_col">
                <label class="personalData_label labeltext">Дата рождения:</label>

                <? $selectedDay = $form->getBirthday() ? $form->getBirthday()->format('j') : '' ?>
                <select id="user_birthday_day" name="user[birthday][day]">
                    <? foreach (array_merge([''], range(1, 31)) as $day):  ?>
                        <option value="<?= $day ?>"<? if ((int)$day == (int)$selectedDay): ?> selected="selected"<? endif ?>><?= $day ?></option>
                    <? endforeach ?>
                </select>

                <? $selectedMonth = $form->getBirthday() ? $form->getBirthday()->format('n') : '' ?>
                <select id="user_birthday_month" name="user[birthday][month]">
                    <? foreach (array_merge([''], range(1, 12)) as $month): ?>
                        <option value="<?= $month ?>"<? if ((int)$month == (int)$selectedMonth): ?> selected="selected"<? endif ?>><?= $month ?></option>
                    <? endforeach ?>
                </select>

                <? $selectedYear = $form->getBirthday() ? $form->getBirthday()->format('Y') : '' ?>
                <select id="user_birthday_year" name="user[birthday][year]">
                    <? foreach (array_merge([''], range(date("Y")-6, 1930)) as $year): ?>
                        <option value="<?= $year ?>"<? if ((int)$year == (int)$selectedYear): ?> selected="selected"<? endif ?>><?= $year ?></option>
                    <? endforeach ?>
                </select>
            </div>

            <div class="personalData_col">
                <label class="personalData_label labeltext">Пол:</label>

                <select name="user[sex]">
                    <? foreach (['' => '', '1' => 'мужской', '2' => 'женский'] as $sexValue => $sexName): ?>
                        <option value="<?= $sexValue ?>"<? if ((int)$sexValue == (int)$form->getSex()): ?> selected="selected"<? endif ?>><?= $sexName ?></option>
                    <? endforeach ?>
                </select>
            </div>

            <div class="personalData_warn">
                <div class="personalData_warn_text">
                    Одно из полей обязательно для заполнения!
                </div>
            </div>

            <label class="personalData_label labeltext">E-mail:</label>
            <input class="personalData_text textfield" type="email"  value="<?= $form->getEmail() ?>" name="user[email]" <? if ($form->getIsDisabled()): ?>readonly<? endif ?> />

            <label class="personalData_label labeltext">Мобильный телефон:</label>
            <input class="personalData_text textfield js-lk-mobilePhone" type="text" value="<?= $form->getMobilePhone() ?>" name="user[mobile_phone]" class="text" <? if ($form->getIsDisabled()): ?>readonly<? endif ?> />

            <label class="personalData_label labeltext">Домашний телефон:</label>
            <input class="personalData_text textfield js-lk-homePhone" type="text" value="<?= $form->getHomePhone() ?>" name="user[home_phone]" />

            <? if (isset($bonusCards) && is_array($bonusCards)): ?>
                <? foreach ($bonusCards as $card):
                    if (!$card instanceof \Model\Order\BonusCard\Entity) continue;

                    $userCardNumber = null;
                    if ((bool)$form->getBonusCard() && is_array($form->getBonusCard())) {
                        foreach ($form->getBonusCard() as $userCard) {
                            if (
                                !array_key_exists('bonus_card_id', $userCard) ||
                                !array_key_exists('number', $userCard) ||
                                $userCard['bonus_card_id'] != $card->getId()
                            ) {
                                continue;
                            }

                            $userCardNumber = $userCard['number'];
                        }
                    } ?>

                    <label class="personalData_label labeltext" >Номер карты &quot;<?= $page->escape($card->getName()) ?>&quot;:</label>
                    <input type="text" id="user_bonus_card_<?= $card->getId() ?>" value="<?= $page->escape($userCardNumber) ?>" name="user[bonus_card][<?= $card->getId() ?>]" data-mask="<?= $card->getMask() ?>" class="personalData_text textfield jsCardNumber" />
                <? endforeach ?>
            <? endif ?>

            <label class="personalData_label labeltext">Род деятельности:</label>
            <input class="personalData_text textfield" type="text" value="<?= $form->getOccupation() ?>" name="user[occupation]" />
        </fieldset>

        <fieldset class="personalData_right">
            <legend class="legend">Пароль</legend>

            <p style="xs">Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.</p>
            <label class="labeltext">Старый пароль:</label>
            <input type="password" class="textfield personalData_text" name="password_old" autocomplete="off" />

            <label class="labeltext">Новый пароль:</label>
            <input type="password" class="textfield personalData_text" name="password_new" autocomplete="off" />

            <!--<p style="xs">Внимание! После смены пароля Вам придет письмо и SMS с новым паролем</p>-->
        </fieldset>

        <fieldset class="personalData_clear">
            <input class="btnsubmit" type="submit" value="Сохранить изменения" />
        </fieldset>
    </form>
</div>
