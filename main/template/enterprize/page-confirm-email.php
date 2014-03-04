<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $error           string
 * @var $message         string
 */
?>

<? $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []) ?>

<div class="titleForm">Подтверди e-mail</div>

<? if ($error): ?>
    <p class="red enterprizeWar"><?= $error ?></p>
<? endif ?>
<? if ($message): ?>
    <p class="green enterprizeWar"><?= $message ?></p>
<? endif ?>

<div class="enterprizeConfirm">
    <p class="textConfirm">Мы отправили специальное письмо на  <strong><?= isset($data['email']) ? $data['email'] : '' ?></strong></p>

    <p class="textConfirm">Ещё какой-то текст про то, почему письмо может не дойти, попасть в спам и т.д. Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском ...</p>

    <form action="<?= $page->url('enterprize.confirmEmail.create') ?>" method="post">
        <input type="hidden" name="isRepeatRending" value="true" />

        <input class="confirmCode bigbutton" type="submit" value="Отправить повторно" />
    </form>
</div>

</div>