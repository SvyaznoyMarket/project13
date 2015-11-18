<?php
/**
 * @var $page         \View\User\OrdersPage
 * @var $helper       \Helper\TemplateHelper
 * @var $user         \Session\User
 * @var $coupons      \Model\EnterprizeCoupon\Entity[]
 * @var $couponChunk  \Model\EnterprizeCoupon\Entity[]
 * @var $couponsByRow array
 */
?>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    <div class="personalPage">

        <div class="private-sections private-sections_gray private-sections_p20 grid ">
        <? foreach ($couponsByRow as $couponChunk): ?>
            <div class="js-ep-container">
                <ul class="private-ep-list clearfix">
                <? foreach ($couponChunk as $coupon): ?>
                <?
                    $sliderContainerId = sprintf('id-coupon-container-%s', $coupon->getToken() ? md5($coupon->getToken()) : uniqid());
                ?>
                    <li
                        class="private-ep-list__cell js-ep-item"
                        data-slider="<?= $helper->json([
                            'url' => $helper->url('enterprize.slider', ['enterprizeToken' => $coupon->getToken(), 'template' => 'user']),
                        ]) ?>"
                        data-relation="<?= $helper->json([
                            'container' => '.' . $sliderContainerId,
                        ]) ?>"
                    >
                        <div class="private-ep-list__item ">
                            <span class="ep-coupon" style="background-image: url(<?= $coupon->getBackgroundImage() ?>);">
                                <span class="ep-coupon__inner">
                                    <span class="ep-coupon__ico"><? if ($image = $coupon->getImage()): ?><img src="<?= $image ?>"><? endif ?></span>
                                    <span class="ep-coupon__desc"><?= $coupon->getName() ?></span>
                                    <span class="ep-coupon__price">
                                        <?= $helper->formatPrice($coupon->getPrice()) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                        <? if ($coupon->getIsCurrency()): ?>
                                            <span class="rubl">p</span>
                                        <? endif ?>
                                    </span>
                                </span>
                            </span>
                        </div>
                    </li>
                <? endforeach ?>
                </ul>

                <div class="private-ep-list__info clearfix">
                <? foreach ($couponChunk as $coupon): ?>
                <?
                    $sliderContainerId = sprintf('id-coupon-container-%s', $coupon->getToken() ? md5($coupon->getToken()) : uniqid());
                    $discount = $coupon->getDiscount();
                ?>
                    <div class="grid__cell grid__cell_2 ep-info js-ep-item-info">
                        <div class="ep-info__desc">
                            <h4 class="ep-info__desc-title"><?= $coupon->getName() ?></h4>

                            <div class="ep-info__desc-duration">
                            <? if ($date = $coupon->getStartDate()): ?>
                                с <span class="ep-info__desc-duration-start"><?= $date->format('d.m.Y') ?></span>
                            <? endif ?>
                            <? if ($date = $coupon->getEndDate()): ?>
                                по <span class="ep-info__desc-duration-end"><?= $date->format('d.m.Y') ?></span>
                            <? endif ?>
                            </div>
                            <div class="ep-info__desc-timer">
                                <p class="ep-info__desc-timer-desc">До конца действия осталось</p>

                                <div class="ep-info__desc-timer-report js-countdown-out js-countdown" data-expires="<?= (($discount && $discount->getEndDate()) ? $discount->getEndDate()->getTimestamp() : null) ?>"></div>
                            </div>
                            <p class="ep-info__desc-txt">
                                <?= $coupon->setSegmentDescription() ?>
                            </p>
                        </div>

                        <div class="ep-info__product">
                            <div class="ep-info__row clearfix">
                                <h4 class="ep-info__product-title">
                                    Действует на товары
                                </h4>

                                <? if ($coupon->getLink()): ?>
                                    <a class="ep-info__product-all" href="<?= $coupon->getLink() ?>">Посмотреть все</a>
                                <? endif ?>
                            </div>
                            <div class="<?= $sliderContainerId ?> js-user-slider-container ep-info__product-slide" data-position="Private.enterprize"></div>
                        </div>
                    </div>
                <? endforeach ?>
                </div>
            </div>
        <? endforeach ?>

        <? if (!$couponsByRow): ?>
            <ul class="private-ep-list">
                <li class="private-ep-list__cell js-ep-item">
                    <div class="private-ep-list__item ">
                        <span class="ep-coupon" style="background-image: url(/styles/personal-page/img/fishki.png);"></span>
                        <div class="private-ep-list__img-desc">Получи фишки EnterPrize</div>
                    </div>
                </li>
            </ul>
        <? endif ?>
        </div>
    </div>

</div>
