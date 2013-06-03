<?php
/**
 * @var $page       \View\User\IndexPage
 * @var $user       \Session\User
 * @var $orderCount int
 */
?>

<div class="fl width315">

    <div class="font16 orange pb10">Моя персональная информация</div>
    <ul class="leftmenu pb20">
        <li>
            <a href="<?= $page->url('user.edit') ?>">Изменить мои данные</a>
        </li>
        <li>
            <a href="<?= $page->url('user.changePassword') ?>">Изменить пароль</a>
        </li>
        <? if ($user->getEntity()->getCity()): ?>
        <li>
            Регион: <strong><?= $user->getEntity()->getCity()->getName() ?></strong> (<a class="jsChangeRegion" data-url="<?= $page->url('region.init') ?>" data-autoresolve-url="<?= $page->url('region.autoresolve') ?>" style="cursor: pointer">изменить</a>)
        </li>
        <? endif ?>
    </ul>

    <div class="font16 orange pb10">Мои товары</div>
    <ul class="leftmenu pb20">
        <li>
            <a href="<?= $page->url('user.order') ?>">Мои заказы</a> (<?= $orderCount ?>)
        </li>
    </ul>

    <div class="font16 orange pb10">cEnter защиты прав потребителей </div>
    <ul class="leftmenu pb20">
        <li>
            <a href="http://my.enter.ru/community/pravo">Адвокат клиента</a>
        </li>
    </ul>

</div>

<? if (false): // TODO: временно убрали из-за проблем с ofsys ?>
<form action="<?= $page->url('user.subscribe') ?>" method="post">
    <div class="fr width315">
        <div class="font16 orange pb10">Рассылка по электронной почте</div>
        <label class="bSubscibe <? if ($user->getEntity()->getIsSubscribed()): ?>checked<? endif ?>">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe"<? if ($user->getEntity()->getIsSubscribed()): ?> checked="checked" <? endif ?> />
        </label>
        <input type="submit" class="fr button bigbutton" value="Сохранить" tabindex="10"/>
        <div class="clear"></div>
    </div>
</form>
<? endif ?>