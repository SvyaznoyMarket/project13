<?php
/**
 * @var $page       \View\User\IndexPage
 * @var $user       \Session\User
 * @var $orderCount int
 */


$userEntity = $user->getEntity();
$userMail = $userEntity->getEmail();
?>

<div class="user fl width315">

    <div class="title">Моя персональная информация</div>

    <ul class="leftmenu userInfoAction">
        <li>
            <a href="<?= $page->url('user.edit') ?>">Изменить мои данные</a>
        </li>
        <li>
            <a href="<?= $page->url('user.changePassword') ?>">Изменить пароль</a>
        </li>
        <? if ($user->getEntity()->getCity()): ?>
        <li>
            Регион: <strong><?= $user->getEntity()->getCity()->getName() ?></strong> (<a class="jsChangeRegion" data-url="<?= $page->url('region.init') ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>" style="cursor: pointer">изменить</a>)
        </li>
        <? endif ?>
    </ul>

    <div class="title">Мои товары</div>
    <ul class="userInfoAction leftmenu">
        <li>
            <a href="<?= $page->url('user.order') ?>">Мои заказы</a> (<?= $orderCount ?>)
        </li>
    </ul>

    <? if (\App::config()->subscribe['enabled']): ?>
    <div class="title">Подписка</div>

    <ul class="userInfoAction bInputList leftmenu">
        <li>
            Акции, новости и специальные предложения
            <form class="clearfix" action="<?= $page->url('user.subscribe') ?>" method="post">
                <label class="emailCheckbox bSubscibe clearfix <?=($userMail) ?
                    (
                        ($user->getEntity()->getIsSubscribed()) ? 'checked' : ''
                    ) : 'hidden' ?>">
                    <b></b> Email
                    <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="bCustomInput subscibe"<? if ($user->getEntity()->getIsSubscribed()): ?> checked="checked" <? endif ?> />
                </label>

                <div id="emailWrapper" class="width418 <?= !empty($emailTmpCheck) ? '' : 'hf' ?>">
                    <span class="width205">Email:</span>
                    <input type="text" id="user_email" value="<?= $user->getEntity()->getEmail() ?>" name="email" class="text width205" />
                </div>


                <label class="smsCheckbox bSubscibe clearfix <? if ($user->getEntity()->getIsSubscribedViaSms() || !empty($smsTmpCheck)): ?>checked<? endif ?>">
                    <b></b> SMS
                    <input type="checkbox" name="subscribe_sms" value="1" autocomplete="off" class="bCustomInput smsCheckbox subscibe"<? if ($user->getEntity()->getIsSubscribedViaSms() || !empty($smsTmpCheck)): ?> checked="checked" <? endif ?> />
                </label>

                <div style="margin-top: 5px;" id="mobilePhoneWrapper" class="width418 <?= !empty($smsTmpCheck) ? '' : 'hf' ?>">
                    <span style="line-height: 28px;" class="width205">Мобильный телефон:</span>
                    <input type="text" id="user_mobile_phone" value="<?= $user->getEntity()->getMobilePhone() ?>" name="mobile_phone" class="text" />
                </div>

                <div class="red pt10 pb10 width418"><?= empty($error) ? '' : $error ?></div>

                <input type="submit" class="btnSave button bigbutton" value="Сохранить" tabindex="10"/>
            </form>
        </li>
    </ul>
    <? endif ?>

    <div class="title">cEnter защиты прав потребителей </div>
    <ul class="userInfoAction leftmenu">
        <li>
            <a href="http://my.enter.ru/community/pravo">Адвокат клиента</a>
        </li>
    </ul>

</div>
