<?php
/**
 * @var $page            \View\Layout
 * @var $title           string|null
 * @var $breadcrumbs     array('url' => null, 'name' => null)[]
 * @var $hasSearch       bool
 * @var $hasSeparateLine bool
 * @var $extendedMargin  bool
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null
 * @var $member          bool
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
$hasSeparateLine = isset($hasSeparateLine) ? (bool)$hasSeparateLine : false;
$extendedMargin = isset($extendedMargin) ? (bool)$extendedMargin : false;

$routeName = \App::request()->attributes->get('route');
$priceNumDecimals = false === strpos((string)$enterpizeCoupon->getPrice(), '.') ? 0 : 2; ?>

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
        <div class="enterPrize mPrivate <?= isset($limit) && $limit === 0 ? 'mDisabled' : ''?>">
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
                                    <span class="cuponPrice">
                                        <?= $page->helper->formatPrice($enterpizeCoupon->getPrice(), $priceNumDecimals) . (!$enterpizeCoupon->getIsCurrency() ? '%' : '') ?>
                                        <? if ($enterpizeCoupon->getIsCurrency()): ?>
                                            <span class="rubl">p</span>
                                        <? endif ?>
                                    </span>
                                <? endif ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="enterPrize__rules"><!-- если пользователь уже получил купон то добавляем класс  mFailed-->
                <div class="rulesText">
                    Фишка со скидкой <strong><?= $page->helper->formatPrice($enterpizeCoupon->getPrice(), $priceNumDecimals) ?><?= !$enterpizeCoupon->getIsCurrency() ? '%' : 'руб' ?></strong> на
                    <strong><a target="_blank" style="text-decoration: underline;" href="<?= $enterpizeCoupon->getLink() ?>"><?= $enterpizeCoupon->getLinkName() ? $enterpizeCoupon->getLinkName() : $enterpizeCoupon->getName() ?></a></strong><br />
                    Минимальная сумма заказа <?= $enterpizeCoupon->getMinOrderSum() ? $enterpizeCoupon->getMinOrderSum() : 0 ?> руб<br />
                    Действует
                    <? if ($enterpizeCoupon->getStartDate() instanceof \DateTime): ?>
                        c <?= $enterpizeCoupon->getStartDate()->format('d.m.Y') ?>
                    <? endif ?>
                    <? if ($enterpizeCoupon->getEndDate() instanceof \DateTime): ?>
                        по <?= $enterpizeCoupon->getEndDate()->format('d.m.Y') ?>
                    <? endif ?>
                </div>

                <? if (isset($limit) && $limit === 0) : ?>
                    <div class="finishedBox">
                        <strong>Фишка закончилась!</strong>

                        <a href="<?= $page->url('enterprize') ?>" class="mBtnOrange">Посмотреть другие фишки</a>
                    </div>
                <? endif; ?>
            </div>

            <? if (!$user->getEntity() && in_array($routeName, ['enterprize', 'enterprize.show', 'enterprize.form.show'])): ?>
                <?= $page->render('enterprize/_auth') ?>
                <div class="enterPrize__logIn">У Вас есть логин и пароль? <a href="<?= \App::router()->generate('user.login') ?>" class="bAuthLink">Войти</a></div>
            <? endif ?>

            <? if (in_array($routeName, ['enterprize.confirmPhone.show', 'enterprize.confirmEmail.show'])): ?>
                <div><a href="<?= \App::router()->generate('enterprize.form.show', ['enterprizeToken' => $enterpizeCoupon->getToken()]) ?>">&lt; Вернуться к анкете</a></div>
            <? endif ?>

            <? if ('enterprize.complete' === $routeName): ?>
                <div class="completeTitleEP">
                    <? if (isset($member) && true === $member): ?>
                        <p class="completeTitleEP__title">Мы отправили номер фишки на Ваш e-mail и мобильный</p>
                    <? else: ?>
                        <div class="completeTitleEP__title">Вы &#8212; в игре!</div>
                        <p class="completeTitleEP__text">Мы отправили номер фишки на Ваш e-mail и мобильный</p>
                    <? endif ?>
                </div>
            <? endif ?>
        </div>
    <? endif ?>

    <? if ($title): ?><div class="titleForm"><?= $title ?></div><? endif ?>

    <div class="clear<? if ($extendedMargin): ?><? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <? endif ?>
</div>
