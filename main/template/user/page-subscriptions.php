<?php
/**
 * @var $page       \View\User\OrderPage
 */
?>

<?= $page->render('user/_menu', ['page' => $page]) ?>

<div class="personalPage">
    <div class="personalTitle">Подписки</div>

    <form action="" class="personalSubscr">
        <fieldset class="personalSubscr_row">
            <legend class="legend">Email</legend>

            <input class="jsCustomRadio customInput customInput-bigCheck" id="email" type="checkbox"  name="" checked />
            <label class="customLabel customLabel-bigCheck" for="email">Акции, новости и специальные предложения </label>

        </fieldset>

        <fieldset class="personalSubscr_row">
            <legend class="legend">SMS</legend>

            <input class="jsCustomRadio customInput customInput-bigCheck" id="sms" type="checkbox" name="" />
            <label class="customLabel customLabel-bigCheck" for="sms">Акции, новости и специальные предложения </label>

        </fieldset>

        <fieldset class="personalSubscr_clear">
            <input class="btnsubmit" type="submit" value="Сохранить" />
        </fieldset>
    </form>
</div>