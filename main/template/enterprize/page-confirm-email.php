<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $user   \Session\User
 * @var $error string
 */

$userEntity = $user->getEntity();
if (!$userEntity) return;
?>

<div>
    <h1>Подтверди e-mail</h1>
    <div class="clear"></div>

    <? if ($error): ?>
        <p class="red"><?= $error ?></p>
    <? endif ?>

    <div>
        <div>Мы отправили специальное письмо на  <b><?= $userEntity->getEmail() ?></b></div>

        <div>
            ﻿Ещё какой-то текст про то, почему письмо может не дойти, попасть в спам и т.д. Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском ...
        </div>

        <form action="<?= $page->url('enterprize.confirmEmail.create') ?>" method="post">
            <fieldset>
                <input type="submit" value="отправить повторно" />
            </fieldset>
        </form>
    </div>

</div>