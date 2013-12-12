<?php
/**
 * @var $page            \View\Layout
 * @var $title           string|null
 * @var $breadcrumbs     array('url' => null, 'name' => null)[]
 * @var $hasSearch       bool
 * @var $hasSeparateLine bool
 * @var $extendedMargin  bool
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null
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
            <a href="<?= $page->url('enterprize') ?>">
                <h1 class="enterPrize__logo">Enter Prize</h1>
            </a>

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

            <ul class="enterPrize__rules clearfix"><!-- если пользователь уже получил купон то добавляем класс  mFailed-->
                <li class="enterPrize__rules__item">Для того, чтобы получить вашу скидку,<br/>заполните недостающие поля в вашем профиле</li>
            </ul>
        </div>
    <? endif ?>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <div class="clear<? if ($extendedMargin): ?> pb20<? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <div class="line"></div>
    <? endif ?>
</div>
