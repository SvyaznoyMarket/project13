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
                    <li class="private-ep-list__cell js-ep-item">
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

                                <div class="ep-info__desc-timer-report js-coupon-date" data-value="<?= $helper->json(['date' => ($discount && ($endDate = $discount->getEndDate())) ? $endDate->getTimestamp() : null ]) ?>">
                                    <span data-name="day">3 д</span>
                                    <span data-name="hour">19 ч</span>
                                    <span data-name="minute">52 мин</span>
                                    <span data-name="second">51 с</span>
                                </div>
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

                                <a class="ep-info__product-all" href="#">
                                    Посмотреть все
                                </a>
                            </div>
                            <div class="js-slider-2 ep-info__product-slide" data-position="Private.enterprize" data-slider="<?= $helper->json([
                                'url' => $helper->url('enterprize.slider', ['enterprizeToken' => $coupon->getToken(), 'template' => 'user']),
                            ]) ?>">
                            </div>
                        </div>
                    </div>
                <? endforeach ?>
                </div>
            </div>
        <? endforeach ?>
        </div>
    </div>

</div>
