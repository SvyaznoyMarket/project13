<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $user   \Session\User
 */

$userEntity = $user->getEntity();
if (!$userEntity) return;
?>

<div>
    <h1>Подтверди номер мобильного</h1>
    <div class="clear"></div>

    <div>

        <div>Мы отправили номер мобильного на номер <b><?= $userEntity->getMobilePhone() ?><?//= preg_replace('/(\d{1,3})(\d{1,3})(\d{1,2})(\d{1,2})/i', '+7 ($1) $2-$3-$4', $userEntity->getEntity()) // должен быть формат +7 999 777-11-22 ?></b></div>

        <form action="<?= $page->url('enterprize.confirmPhone.check') ?>" method="post">
            <fieldset>
                <label>Введи код:</label>
                <div><input type="text" name="code" /></div>

                <input type="button" value="Новый код" />
                <input type="submit" value="Подтвердить" />
            </fieldset>
        </form>
    </div>
</div>