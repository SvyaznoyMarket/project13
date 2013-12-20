<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="fixedTopBar mFixed<? if ('product' == \App::request()->attributes->get('route')): ?> mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
    <?= $page->slotUserbarContent() ?>

    <div class="fixedTopBar__cart mEmpty">
        <a class="fixedTopBar__cartLink" href="<?=  $page->url('cart') ?>">
            <span class="fixedTopBar__cartTitle">Корзина</span>
        </a>
    </div>

    <div class="fixedTopBar__logIn mLogin"><!-- Добавляем класс-модификатор mLogin, если пользователь не залогинен -->
        <a href="<?= $page->url('user.login') ?>" class="fixedTopBar__logInLink bAuthLink">Войти</a>
        <span class="transGrad"></span>

        <div class="fixedTopBar__dd fixedTopBar__logOut">
            <div class="enterPrize">
                <div class="enterPrize__text">
                    <strong class="title">Enter Prize</strong>
                    Выбери фишку со скидкой на любой товар в ENTER!
                </div>

                <a href="<?= $page->url('enterprize') ?>" class="mBtnOrange enterPrize__reglink">Выбрать</a>
            </div>
        </div>
    </div>
</div>
