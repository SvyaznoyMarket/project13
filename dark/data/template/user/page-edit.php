<?php
/**
 * @var $page \View\User\EditPage
 * @var $form \View\User\EditForm
 */
?>

<form action="<?= $page->url('user.edit') ?>" class="form" method="post">
    <div class="fl width430">


        <div class="pb10">
            <label for="user_first_name">Имя</label>:
        </div>
        <input type="text" id="user_first_name" value="<?= $form->getFirstName() ?>" name="user[first_name]" class="text width418 mb10" />

        <div class="pb10">
            <label for="user_middle_name">Отчество</label>:
        </div>
        <input type="text" id="user_middle_name" value="<?= $form->getMiddleName() ?>" name="user[middle_name]" class="text width418 mb10" />

        <div class="pb10">
            <label for="user_last_name">Фамилия</label>:
        </div>
        <input type="text" id="user_last_name" value="<?= $form->getLastName() ?>" name="user[last_name]" class="text width418 mb10" />

        <div class="pb10">
            <label for="user_gender">Пол</label>:

        </div>
        <div class="selectbox selectbox170"><i></i>
            <span class="select" id="selectuser[gender]"><?= $form->getSexName() ?></span>
            <select id="user_gender" name="user[gender]" class="styled">
            <? foreach (array('' => '', '1' => 'мужской', '2' => 'женский') as $sexValue => $sexName): ?>
                <option value="<?= $sexValue ?>"<? if ($sexValue == $form->getSex()): ?> selected="selected"<? endif ?>><?= $sexName ?></option>
            <? endforeach ?>
            </select>
        </div>
        <div class="clear pb15"></div>

        <div class="pb10">
            <label for="user_email">E-mail</label>:
        </div>
        <input type="text" id="user_email" value="<?= $form->getEmail() ?>" name="user[email]" class="text width418 mb10" />

        <div class="pr fr">
            <div class="help">
                <div class="doublehelp">Одно из полей обязательно для заполнения!</div>
            </div>
        </div>

        <div class="pb10">
            <label for="user_phonenumber">Мобильный телефон</label>:
        </div>
        <input type="text" id="user_phonenumber" value="<?= $form->getMobilePhone() ?>" name="user[phonenumber]" class="text width418 mb10" />

        <div class="pb10">
            <label for="user_phonenumber_city">Домашний телефон</label>:
        </div>
        <input type="text" id="user_phonenumber_city" value="<?= $form->getHomePhone() ?>" name="user[phonenumber_city]" class="text width418 mb10" />

        <div class="pb10">
            <label for="user_skype">Skype</label>:
        </div>
        <input type="text" id="user_skype" value="<?= $form->getSkype() ?>" name="user[skype]" class="text width418 mb10" />

        <div class="pb10">
            <label>Дата рождения</label>:
        </div>
        <div class="selectbox selectbox75 fl mr10">
            <i></i>
            <? $selectedDay = $form->getBirthday() ? $form->getBirthday()->format('d') : '' ?>
            <span class="select" id="selectuser[birthday][day]"><?= $selectedDay ?></span>
            <select id="user_birthday_day" name="user[birthday][day]" class="styled">
            <? foreach (array_merge(array(''), range(1, 31)) as $day): ?>
                <option value="<?= $day ?>"<? if ((int)$day == (int)$selectedDay): ?> selected="selected"<? endif ?>><?= $day ?></option>
            <? endforeach ?>
            </select>
        </div>
        <div class="selectbox selectbox98 fl mr10">
            <i></i>
            <? $selectedMonth = $form->getBirthday() ? $form->getBirthday()->format('m') : '' ?>
            <span class="select" id="selectuser[birthday][month]"><?= $selectedMonth ?></span>
            <select id="user_birthday_month" name="user[birthday][month]" class="styled">
            <? foreach (array_merge(array(''), range(1, 12)) as $month): ?>
                <option value="<?= $month ?>"<? if ((int)$month == (int)$selectedMonth): ?> selected="selected"<? endif ?>><?= $month ?></option>
            <? endforeach ?>
            </select>
        </div>
        <div class="selectbox selectbox75 fl">
            <i></i>
            <? $selectedYear = $form->getBirthday() ? $form->getBirthday()->format('Y') : '' ?>
            <span class="select" id="selectuser[birthday][year]"><?= $selectedYear?></span>
            <select id="user_birthday_year" name="user[birthday][year]" class="styled">
            <? foreach (array_merge(array(''), range(2005, 1930)) as $year): ?>
                <option value="<?= $year ?>"<? if ((int)$year == (int)$selectedYear): ?> selected="selected"<? endif ?>><?= $year ?></option>
            <? endforeach ?>
            </select>
        </div>

        <div class="clear pb15"></div>
        <div class="pb10">
            <label for="user_occupation">Род деятельности</label>:
        </div>
        <input type="text" id="user_occupation" value="<?= $form->getOccupation() ?>" name="user[occupation]" class="text width418 mb10" />


        <div class="clear pb20"></div>
        <input type="submit" value="Сохранить изменения" id="bigbutton" class="button yellowbutton">

        <div class="pb10"></div>
    </div>
</form>
