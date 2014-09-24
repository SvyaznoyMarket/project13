<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="topbarfix topbarfix-fx<? if ('product' == \App::request()->attributes->get('route')): ?> mProdCard<? endif ?>" data-value="<?= $page->json($page->slotUserbarContentData()) ?>">
    <div class="topbarfix_cart mEmpty">
        <a class="topbarfix_cart_lk" href="<?=  $page->url('cart') ?>">
            <span class="topbarfix_cart_tl">Корзина</span>
        </a>
    </div>

    <div class="topbarfix_cmpr">
        <a href="" class="topbarfix_cmpr_tl">Сравнение</a>
    </div>

    <div class="topbarfix_log topbarfix_log-unl"><!-- Добавляем класс-модификатор topbarfix_log-unl, если пользователь не залогинен -->
        <a href="<?= $page->url('user.login') ?>" class="topbarfix_log_lk bAuthLink">Личный кабинет</a>
        <?= $page->slotUserbarEnterprize() ?>
    </div>

    <?= $page->slotUserbarContent() ?>
</div>
