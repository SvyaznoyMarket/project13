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
        <a href="<?= $page->url('user.login') ?>" class="fixedTopBar__logInLink bAuthLink">Личный кабинет</a>
        <span class="transGrad"></span>

        <?= $page->slotUserbarEnterprize() ?>
    </div>
</div>
