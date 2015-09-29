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
 * @var $isPartnerCoupon bool
 */
?>

<? $routeName = \App::request()->attributes->get('route'); ?>

<? if ($enterpizeCoupon):

    $url = $enterpizeCoupon->getLink();
    $name = $enterpizeCoupon->getName();
    $linkName = $enterpizeCoupon->getLinkName() ? $enterpizeCoupon->getLinkName() : $name;
    $imageUrl = $enterpizeCoupon->getImage();

    // партнерский купон
    if (isset($isPartnerCoupon) && (bool)$isPartnerCoupon) {
        $url = $enterpizeCoupon->getPartnerUrl();
    }

    ?>

    <div class="ep-box <?= isset($limit) && $limit === 0 ? 'ep-box--hidden' : ''?>">
        <a class="ep-logo" href="<?= $page->url('enterprize') ?>"></a>

        <div class="ep-list clearfix">
            <div class="ep-list__i">
                <div class="ep-list__lk">
                    <span class="ep-coupon"<? if ($enterpizeCoupon->getBackgroundImage()): ?> style="background-image: url(<?= $enterpizeCoupon->getBackgroundImage() ?>);"<? endif ?>>
                        <span class="ep-coupon__inner">
                            <? if ($imageUrl): ?>
                                <span class="ep-coupon__ico"><img src="<?= $imageUrl ?>" /></span>
                            <? endif ?>

                            <? if ($name): ?>
                                <span class="ep-coupon__desc"><?= $name ?></span>
                            <? endif ?>

                            <? if ($enterpizeCoupon->getPrice()): ?>
                                <span class="ep-coupon__price">
                                    <?= $page->helper->formatPrice($enterpizeCoupon->getPrice()) . (!$enterpizeCoupon->getIsCurrency() ? '%' : '') ?>
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

        <div class="ep-rules"><!-- если пользователь уже получил купон то добавляем класс  mFailed-->
            <? if ($enterpizeCoupon->getPartnerImageUrl()) : ?>
                <div class="ep-rules__img">
                    <img src="<?= $enterpizeCoupon->getPartnerImageUrl() ?>" alt="" />
                </div>
            <? endif; ?>

            <div class="ep-rules__tx">
                Фишка со скидкой <strong><?= $page->helper->formatPrice($enterpizeCoupon->getPrice()) ?><?= !$enterpizeCoupon->getIsCurrency() ? '%' : ' <span class="rubl">p</span>' ?></strong>
                <? if ($linkName): ?>
                    <?= ' на ' ?><strong><a target="_blank" style="text-decoration: underline;" href="<?= $url ?>"><?= $linkName ?></a></strong>
                <? endif ?>

                <? if ($enterpizeCoupon->getSegmentDescription()): ?>
                    <br />
                    <?= $enterpizeCoupon->getSegmentDescription() ?>
                <? endif ?>

                <br />
                Минимальная сумма заказа <?= $enterpizeCoupon->getMinOrderSum() ? $enterpizeCoupon->getMinOrderSum() : 0 ?> <span class="rubl">p</span><br />
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
            <div class="ep-box__login">У Вас есть логин и пароль? <a href="<?= \App::router()->generate('user.login') ?>" class="js-login-opener">Войти</a></div>
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