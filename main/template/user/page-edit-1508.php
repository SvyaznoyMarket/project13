<?php
/**
 * @var $page       \View\User\EditPage
 * @var $helper     \Helper\TemplateHelper
 * @var $form       \View\User\EditForm
 * @var $flash      array|null
 * @var $bonusCards array
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$passwordFormResult = [];
$formResult = [];
if (isset($flash['form'])) {
    if ('user.password' === $flash['form']) {
        $passwordFormResult = $flash;
    } else if ('user' === $flash['form']) {
        $formResult = $flash;
    }
}

$selectedDay = $form->getBirthday() ? $form->getBirthday()->format('j') : '';
$selectedMonth = $form->getBirthday() ? $form->getBirthday()->format('n') : '';
$selectedYear = $form->getBirthday() ? $form->getBirthday()->format('Y') : '';
?>

<div class="personal">

    <? if ($flash !== null) : ?>
        <p class="<?= $flash['type'] == 'success' ? 'green' : 'red' ?>"><?= $flash['message'] ?></p>
    <? endif; ?>

    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div class="personal__password">
        <div class="personal__sub-head">Изменить пароль</div>
        <p>Надежный пароль должен содержать от 6 до 16 знаков следующих трех видов: прописные буквы, строчные буквы, цифры или символы, но не должен включать широко распространенные слова и имена.</p>
        <form class="js-form" action="<?= $page->url('user.update.password') ?>" method="post" data-result="<?= $helper->json($passwordFormResult) ?>">
            <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

            <div class="form-group" data-field-container="password_old">
                <label class="label-control js-label-control">Старый пароль</label>
                <input class="input-control js-input-control" type="password" name="password_old" autocomplete="off" data-field="password_old" placeholder="Старый пароль">
            </div>
            <div class="form-group" data-field-container="password_new">
                <label class="label-control js-label-control">Новый пароль</label>
                <input class="input-control js-input-control" type="password" name="password_new" autocomplete="off" data-field="password_new" placeholder="Новый пароль">
            </div>
            <div class="form-group" data-field-container="password_repeat">
                <label class="label-control js-label-control">Повторите пароль</label>
                <input class="input-control js-input-control" type="password" name="password_repeat" autocomplete="off" data-field="password_repeat" placeholder="Повторите пароль">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить</button>
            </div>
        </form>
    </div>

    <div class="personal__info">
        <form class="js-form" action="<?= $page->url('user.update') ?>" method="post" data-result="<?= $helper->json($formResult) ?>">
            <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

            <div class="form-group" data-field-container="first_name">
                <label class="label-control js-label-control">Имя</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getFirstName() ?>" name="user[first_name]" data-field="first_name" placeholder="Имя">
            </div>
            <div class="form-group" data-field-container="middle_name">
                <label class="label-control js-label-control">Отчество</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getMiddleName() ?>" name="user[middle_name]" data-field="middle_name" placeholder="Отчество">
            </div>
            <div class="form-group" data-field-container="last_name">
                <label class="label-control js-label-control">Фамилия</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getLastName() ?>" name="user[last_name]" data-field="last_name" placeholder="Фамилия">
            </div>
            <div class="form-group inline" data-field-container="birthday.day">
                <label class="label-control always-show">Дата рождения</label>
                <div class="custom-select custom-select--day">
                    <select class="custom-select__inn" name="user[birthday][day]" data-field="birthday.day">
                    <? foreach (array_merge([''], range(1, 31)) as $day):  ?>
                        <option class="custom-select__i" value="<?= $day ?>"<? if ((int)$day == (int)$selectedDay): ?> selected="selected"<? endif ?>><?= $day ?></option>
                    <? endforeach ?>
                    </select>
                </div>
                <div class="custom-select custom-select--month" data-field-container="birthday.month">
                    <select class="custom-select__inn" name="user[birthday][month]" data-field="birthday.month">
                    <? foreach (array_merge([''], range(1, 12)) as $month): ?>
                        <option class="custom-select__i" value="<?= $month ?>"<? if ((int)$month == (int)$selectedMonth): ?> selected="selected"<? endif ?>><?= $month ?></option>
                    <? endforeach ?>
                    </select>
                </div>

                <div class="custom-select custom-select--year" data-field-container="birthday.year">
                    <select class="custom-select__inn" name="user[birthday][year]" data-field="birthday.year">
                    <? foreach (array_merge([''], range(date('Y')-6, 1939)) as $year): ?>
                        <option class="custom-select__i" value="<?= $year ?>"<? if ((int)$year == (int)$selectedYear): ?> selected="selected"<? endif ?>><?= $year ?></option>
                    <? endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group inline right">
                <label class="label-control always-show">Пол</label>
                <div class="custom-select custom-select--sex" data-field-container="sex">
                    <select class="custom-select__inn" name="user[sex]" data-field="sex">
                    <? foreach (['' => '', '1' => 'мужской', '2' => 'женский'] as $sexValue => $sexName): ?>
                        <option class="custom-select__i" value="<?= $sexValue ?>"<? if ((int)$sexValue == (int)$form->getSex()): ?> selected="selected"<? endif ?>><?= $sexName ?></option>
                    <? endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group" data-field-container="email">
                <label class="label-control always-show js-label-control">Email</label>
                <input class="input-control js-input-control disabled" type="text" value="<?= $form->getEmail() ?>" name="user[email]" <? if ($form->getIsDisabled()): ?>disabled<? endif ?> data-field="email">
            </div>
            <div class="form-group" data-field-container="phone">
                <label class="label-control js-label-control">Мобильный телефон</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getMobilePhone() ?>" name="user[mobile_phone]" <? if ($form->getIsDisabled()): ?>disabled<? endif ?> data-field="phone" placeholder="Мобильный телефон">
            </div>
            <div class="form-group" data-field-container="home_phone">
                <label class="label-control js-label-control">Домашний телефон</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getHomePhone() ?>" name="user[home_phone]" data-field="home_phone" placeholder="Домашний телефон">
            </div>
            <div class="form-group" data-field-container="occupation">
                <label class="label-control js-label-control">Род деятельности</label>
                <input class="input-control js-input-control" type="text" value="<?= $form->getOccupation() ?>" name="user[occupation]" data-field="occupation" placeholder="Род деятельности">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить изменения</button>
            </div>
        </form>

    </div>

</div>
