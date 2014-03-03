<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $user   \Session\User
 * @var $error string
 */

$userEntity = $user->getEntity();
if (!$userEntity) return;
?>

<div class="titleForm">Подтверди e-mail</div>

    <? if ($error): ?>
        <p class="red"><?= $error ?></p>
    <? endif ?>

    <div class="enterprizeConfirm">
        <p class="textConfirm">Мы отправили специальное письмо на  <strong><?= $userEntity->getEmail() ?></strong></p>

        <p class="textConfirm">Ещё какой-то текст про то, почему письмо может не дойти, попасть в спам и т.д. Сайт рыбатекст поможет дизайнеру, верстальщику, вебмастеру сгенерировать несколько абзацев более менее осмысленного текста рыбы на русском ...</p>

        <form action="<?= $page->url('enterprize.confirmEmail.create') ?>" method="post">
            <input class="confirmCode bigbutton" type="submit" value="Отправить повторно" />
        </form>
    </div>

</div>