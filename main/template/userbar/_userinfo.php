<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<?
$checkAuthUrl = $page->url('user.checkAuth');
?>

<li class="userbtn_i topbarfix_log topbarfix_log-unlogin" data-bind="visible: !name()">


    <div class="topbarfix_log_opener js-topbarfixLogin-opener">
        <a href="/login" class="topbarfix_log_lk js-login-opener"><span class="topbarfix_log_tx">Вход</span></a>

        <div class="userbar-dd userbar-dd--account">
            <ul class="user-account id-user-menu">
                <li class="user-account__i">
                    <a href="#" class="user-account__lk js-checkStatus" data-url="<?= $page->url('orderV3.status') ?>">
                        <span class="user-account__text">Проверить статус заказа</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user', [], true)]) ?>">
                        <span class="user-account__text">Личный кабинет</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.orders') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.orders', [], true)]) ?>">
                        <span class="user-account__text">Заказы</span>
                        <i class="user-account__counter" data-count="orderCount"></i>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.favorites') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.favorites', [], true)]) ?>">
                        <span class="user-account__text">Избранное</span>
                        <i class="user-account__counter" data-count="favoriteCount"></i>
                    </a>
                </li>

                <? if (false): ?>
                <li class="user-account__i">
                    <a href="<?= $page->url('user.orders') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.orders', [], true)]) ?>">
                        <span class="user-account__text">Отложенные заказы</span>
                        <i class="user-account__counter" data-count=""></i>
                    </a>
                </li>
                <? endif ?>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.subscriptions') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.subscriptions', [], true)]) ?>">
                        <span class="user-account__text">Подписки</span>
                        <i class="user-account__counter" data-count="subscribeCount"></i>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.address') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.address', [], true)]) ?>">
                        <span class="user-account__text">Адреса</span>
                        <i class="user-account__counter" data-count="addressCount"></i>
                    </a>
                </li>

                <? if (false): ?>
                <li class="user-account__i">
                    <a href="<?= $page->url('user.message') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.message', [], true)]) ?>">
                        <span class="user-account__text">Сообщения</span>
                        <i class="user-account__counter" data-count="messageCount"></i>
                    </a>
                </li>
                <? endif ?>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.edit') ?>" class="user-account__lk js-login-opener" data-check-auth-url="<?= $page->url('user.checkAuth', ['redirect_to' => $page->url('user.edit', [], true)]) ?>">
                        <span class="user-account__text">Личные данные</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.login') ?>" class="user-account__lk user-account__lk_login  js-login-opener">
                        <span class="user-account__text">Войти/Регистрация</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</li>

<li class="userbtn_i topbarfix_log topbarfix_log-login js-topbarfixLogin" data-bind="visible: name()" style="display: none">
    <div class="topbarfix_log_opener js-topbarfixLogin-opener">
        <a href="" class="topbarfix_log_lk" data-bind="attr: { href: link }">
            <!--ko text: firstName--><!--/ko--> <!--ko text: lastName--><!--/ko-->
        </a>

        <div class="userbar-dd userbar-dd--account">
            <ul class="user-account id-user-menu">
                <li class="user-account__i">
                    <a href="#" class="user-account__lk js-checkStatus" data-url="<?= $page->url('orderV3.status') ?>">
                        <span class="user-account__text">Проверить статус заказа</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user') ?>" class="user-account__lk">
                        <span class="user-account__text">Личный кабинет</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.orders') ?>" class="user-account__lk">
                        <span class="user-account__text">Заказы</span>
                        <i class="user-account__counter" data-count="orderCount"></i>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.favorites') ?>" class="user-account__lk">
                        <span class="user-account__text">Избранное</span>
                        <i class="user-account__counter" data-count="favoriteCount"></i>
                    </a>
                </li>

                <? if (false): ?>
                <li class="user-account__i">
                    <a href="<?= $page->url('user.orders') ?>" class="user-account__lk">
                        <span class="user-account__text">Отложенные заказы</span>
                        <i class="user-account__counter" data-count=""></i>
                    </a>
                </li>
                <? endif ?>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.subscriptions') ?>" class="user-account__lk">
                        <span class="user-account__text">Подписки</span>
                        <i class="user-account__counter" data-count="subscribeCount"></i>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.address') ?>" class="user-account__lk">
                        <span class="user-account__text">Адреса</span>
                        <i class="user-account__counter" data-count="addressCount"></i>
                    </a>
                </li>

                <? if (false): ?>
                <li class="user-account__i">
                    <a href="<?= $page->url('user.message') ?>" class="user-account__lk">
                        <span class="user-account__text">Сообщения</span>
                        <i class="user-account__counter" data-count="messageCount"></i>
                    </a>
                </li>
                <? endif ?>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.edit') ?>" class="user-account__lk">
                        <span class="user-account__text">Личные данные</span>
                    </a>
                </li>

                <li class="user-account__i">
                    <a href="<?= $page->url('user.logout') ?>" class="user-account__lk user-account__lk_exit">
                        <span class="user-account__text">Выйти</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Favourite widget -->
    <div class="favourite-userbar-popup-widget"></div>
</li>
