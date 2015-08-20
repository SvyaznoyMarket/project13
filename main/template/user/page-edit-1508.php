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

            <div class="form-group">
                <label class="label-control">Старый пароль</label>
                <input class="input-control" type="password" name="password_old" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="label-control">Новый пароль</label>
                <input class="input-control" type="password" name="password_new" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="label-control">Повторите пароль</label>
                <input class="input-control" type="password" name="password_repeat" autocomplete="off">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить</button>
            </div>
        </form>
    </div>

    <div class="personal__info">
        <form action="<?= $page->url('user.update') ?>" method="post" data-result="<?= $helper->json($formResult) ?>">
            <input type="hidden" name="redirect_to" value="<?= $redirect ?>">

            <div class="form-group">
                <label class="label-control">Имя</label>
                <input class="input-control" type="text" value="<?= $form->getFirstName() ?>" name="user[first_name]">
            </div>
            <div class="form-group">
                <label class="label-control">Отчество</label>
                <input class="input-control" type="text" value="<?= $form->getMiddleName() ?>" name="user[middle_name]">
            </div>
            <div class="form-group">
                <label class="label-control">Фамилия</label>
                <input class="input-control" type="text" value="<?= $form->getLastName() ?>" name="user[last_name]">
            </div>
            <div class="form-group inline">
                <label class="label-control">Дата рождения</label>
                <div class="custom-select custom-select--day">
                    <select class="custom-select__inn" name="user[birthday][day]">
                    <? foreach (array_merge([''], range(1, 31)) as $day):  ?>
                        <option class="custom-select__i" value="<?= $day ?>"<? if ((int)$day == (int)$selectedDay): ?> selected="selected"<? endif ?>><?= $day ?></option>
                    <? endforeach ?>
                    </select>
                </div>
                <div class="custom-select custom-select--month">
                    <select class="custom-select__inn" name="user[birthday][month]">
                    <? foreach (array_merge([''], range(1, 12)) as $month): ?>
                        <option class="custom-select__i" value="<?= $month ?>"<? if ((int)$month == (int)$selectedMonth): ?> selected="selected"<? endif ?>><?= $month ?></option>
                    <? endforeach ?>
                    </select>
                </div>

                <div class="custom-select custom-select--year">
                    <select class="custom-select__inn" name="user[birthday][year]">
                    <? foreach (array_merge([''], range(date('Y')-6, 1939)) as $year): ?>
                        <option class="custom-select__i" value="<?= $year ?>"<? if ((int)$year == (int)$selectedYear): ?> selected="selected"<? endif ?>><?= $year ?></option>
                    <? endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group inline right">
                <label class="label-control">Пол</label>
                <div class="custom-select custom-select--sex">
                    <select class="custom-select__inn" name="user[sex]">
                    <? foreach (['' => '', '1' => 'мужской', '2' => 'женский'] as $sexValue => $sexName): ?>
                        <option class="custom-select__i" value="<?= $sexValue ?>"<? if ((int)$sexValue == (int)$form->getSex()): ?> selected="selected"<? endif ?>><?= $sexName ?></option>
                    <? endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="label-control">email не редактируется</label>
                <input class="input-control disabled" type="text" value="<?= $form->getEmail() ?>" name="user[email]" <? if ($form->getIsDisabled()): ?>disabled<? endif ?>>
            </div>
            <div class="form-group">
                <label class="label-control">Мобильный телефон</label>
                <input class="input-control" type="text" value="<?= $form->getMobilePhone() ?>" name="user[mobile_phone]" <? if ($form->getIsDisabled()): ?>disabled<? endif ?>>
            </div>
            <div class="form-group">
                <label class="label-control">Домашний телефон</label>
                <input class="input-control" type="text" value="<?= $form->getHomePhone() ?>" name="user[home_phone]">
            </div>
            <div class="form-group">
                <label class="label-control">Род деятельности</label>
                <input class="input-control" type="text" value="<?= $form->getOccupation() ?>" name="user[occupation]">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-type btn-type--buy">Сохранить изменения</button>
            </div>
        </form>

    </div>

</div>
