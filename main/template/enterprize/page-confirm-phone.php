<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $user            \Session\User
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $error           string
 */

$data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
?>

<div>
    <h1>Подтверди номер мобильного</h1>
    <div class="clear"></div>

    <? if ($error): ?>
        <p class="red"><?= $error ?></p>
    <? endif ?>

    <div>

        <div>Мы отправили номер мобильного на номер <b><?= $data['mobile'] ? $data['mobile'] : '' ?><?//= preg_replace('/(\d{1,3})(\d{1,3})(\d{1,2})(\d{1,2})/i', '+7 ($1) $2-$3-$4', $userEntity->getEntity()) // должен быть формат +7 999 777-11-22 ?></b></div>

        <form action="<?= $page->url('enterprize.confirmPhone.check') ?>" method="post">
            <input type="hidden" name="enterprizeToken" value="<?= $enterpizeCoupon ? $enterpizeCoupon->getToken() : null ?>" />

            <fieldset>
                <label>Введи код:</label>
                <div><input type="text" name="code" /></div>
                <input type="submit" value="Подтвердить" />
            </fieldset>
        </form>

        <form action="<?= $page->url('enterprize.confirmPhone.create') ?>" method="post">
            <fieldset>
                <input type="submit" value="Новый код" />
            </fieldset>
        </form>
    </div>
</div>