<?php
/**
 * @var $page            \View\Layout
 * @var $title           string|null
 * @var $breadcrumbs     array('url' => null, 'name' => null)[]
 * @var $hasSearch       bool
 * @var $hasSeparateLine bool
 * @var $extendedMargin  bool
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null
 * @var $enterprizeData  array|null
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
$hasSeparateLine = isset($hasSeparateLine) ? (bool)$hasSeparateLine : false;
$extendedMargin = isset($extendedMargin) ? (bool)$extendedMargin : false;
?>

<div class="pagehead">

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default', ['searchQuery' => $page->getParam('searchQuery')]) ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <div class="clear"></div>

    <? if ($enterpizeCoupon): ?>
        <div class="enterPrize mPrivate">
            <a class="enterPrize__logo" href="<?= $page->url('enterprize') ?>"></a>

            <div class="enterPrize__list clearfix">
                <div class="enterPrize__list__item mOrange">
                    <div class="enterPrize__list__link">
                        <span class="cuponImg"<? if ($enterpizeCoupon->getBackgroundImage()): ?> style="background-image: url(<?= $enterpizeCoupon->getBackgroundImage() ?>);"<? endif ?>>
                            <span class="cuponImg__inner">
                                <? if ($enterpizeCoupon->getImage()): ?>
                                    <span class="cuponIco"><img src="<?= $enterpizeCoupon->getImage() ?>" /></span>
                                <? endif ?>

                                <? if ($enterpizeCoupon->getName()): ?>
                                    <span class="cuponDesc"><?= $enterpizeCoupon->getName() ?></span>
                                <? endif ?>

                                <? if ($enterpizeCoupon->getPrice()): ?>
                                    <span class="cuponPrice"><?= $enterpizeCoupon->getPrice() . (!$enterpizeCoupon->getIsCurrency() ? '%' : '') ?> <? if ($enterpizeCoupon->getIsCurrency()): ?><span class="rubl">p</span><? endif ?></span>
                                <? endif ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="enterPrize__rules"><!-- если пользователь уже получил купон то добавляем класс  mFailed-->
                Фишка со скидкой <strong><?= $enterpizeCoupon->getPrice() ?> <?= !$enterpizeCoupon->getIsCurrency() ? '%' : 'руб' ?></strong> на <strong><?= $enterpizeCoupon->getName() ?></strong><br />
                Минимальная сумма заказа <?= $enterpizeCoupon->getMinOrderSum() ? $enterpizeCoupon->getMinOrderSum() : 0 ?> руб<br />
                Действует
                <? if ($enterpizeCoupon->getStartDate() instanceof \DateTime): ?>
                    c <?= $enterpizeCoupon->getStartDate()->format('d.m.Y') ?>
                <? endif ?>
                <? if ($enterpizeCoupon->getEndDate() instanceof \DateTime): ?>
                    по <?= $enterpizeCoupon->getEndDate()->format('d.m.Y') ?>
                <? endif ?>
            </div>

            <? if (!$user->getEntity()): ?>
                <?= $page->render('enterprize/_auth') ?>
                <div class="enterPrize__logIn">У тебя есть логин и пароль? <a href="<?= \App::router()->generate('user.login') ?>" class="bAuthLink">Войти</a></div>
            <? endif ?>

            <? if ('enterprize.complete' === \App::request()->attributes->get('route')): ?>
                <div class="completeTitleEP">
                    <div class="completeTitleEP__title">Ты &#8212; в игре!</div>
                    <p class="completeTitleEP__text">Мы отправили номер фишки на твой e-mail и мобильный</p>
                </div>
            <? endif ?>
        </div>
    <? endif ?>

    <? if (isset($enterprizeData)): ?>
        <div class="jsEnterprizeData" data-value='<?= json_encode($enterprizeData) ?>'></div>
    <? endif ?>


    <? if ($title): ?><div class="titleForm"><?= $title ?></div><? endif ?>

    <div class="clear<? if ($extendedMargin): ?><? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <? endif ?>
</div>
