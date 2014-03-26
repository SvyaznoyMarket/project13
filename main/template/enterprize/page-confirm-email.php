<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $error           string
 * @var $message         string
 */
?>

<? $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []) ?>

<!--div class="titleForm">Подтверди e-mail</div-->

<? if ($error): ?>
    <p class="red enterprizeWar"><?= $error ?></p>
<? endif ?>
<? if ($message): ?>
    <p class="green enterprizeWar"><?= $message ?></p>
<? endif ?>

<div class="enterprizeConfirm">
    <p class="textConfirm">Мы отправили специальное письмо на  <strong><?= isset($data['email']) ? $data['email'] : '' ?></strong></p>

    <p class="textConfirm">Как только e-mail будет подтвержден, мы отправим фишку по e-mail и в SMS.</p>

    <form class="confirmForm" action="<?= $page->url('enterprize.confirmEmail.create') ?>" method="post">
        <input type="hidden" name="isRepeatRending" value="true" />

        <input style="margin-left: 0;" class="confirmCode bigbutton" type="submit" value="Отправить повторно" />
    </form>

    <p style="margin: 30px 0 0 0; font-size: 12px;" class="textConfirm">Если письмо затерялось или обнаружили ошибку, пожалуйста, напишите нам на <a style="text-decoration: underline;" href="mailto:feedback@enter.ru">feedback@enter.ru</a>.</p>
</div>

</div>