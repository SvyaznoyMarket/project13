<?php
/**
 * @var $page   \View\Layout
 * @var $user   \Session\User
 * @var $isForm bool
 */
?>

<?
if (!isset($isForm)) $isForm = true;
?>

<? if (\App::config()->f1Certificate['enabled']): ?>

<div class="clear"></div>

<div class="bF1SaleCard">
    <div class="pl35">
        <? if ((bool)$user->getCart()->getCertificates()): ?>
            <div class="bF1SaleCard_eComplete mGold">
                <p class="font14">Для заказа действует скидка по программе «Под защитой F1» <a class="bF1SaleCard_eDel" href="#" data-url="<?= $page->url('cart.certificate.delete') ?>">отменить</a></p>
            </div>
        <? elseif ((bool)$user->getCart()->getCoupons()): ?>
            <? foreach ($user->getCart()->getCoupons() as $coupon): ?>
            <div class="bF1SaleCard_eComplete mGold">
                <p class="font14">Для заказа действует скидка «<?= $coupon->getName() ?>» <a class="bF1SaleCard_eDel" href="#" data-url="<?= $page->url('cart.coupon.delete') ?>">отменить</a></p>
            </div>
            <? endforeach ?>
        <? elseif ($isForm): ?>
            <div class="bF1SaleCard_eForm<? if ($user->getCart()->hasServices()): ?> m2Coupon<? endif ?>">
                <div class="bF1SaleCard_eRadiogroup">
                    <h3 class="bF1SaleCard_eTitle ">Скидки</h3>
                    <label class="bF1SaleCard_eLabel"><input id="cartCertificateAll" class="bF1SaleCard_eRadio" name="coupon" type="radio" checked="checked" data-url="<?= $page->url('cart.coupon.apply') ?>" /> Код скидки на товары</label>
                    <label class="bF1SaleCard_eLabel"><input id="cartCertificateF1" class="bF1SaleCard_eRadio" name="coupon" type="radio" data-url="<?= $page->url('cart.certificate.apply') ?>" /> Скидки на услуги F1 по карте «Под защитой F1»</label>
                </div>
                <div class="bF1SaleCard_eSingleCoupon">
                    <h3 class="bF1SaleCard_eTitle ">Скидка</h3>
                </div>
                <input id="F1SaleCard_number" class="mr20 width370" type="text" placeholder="Код скидки"/><input id="F1SaleCard_btn" class="yellowbutton button" type="button" value="Применить" />
                <p id="bF1SaleCard_eErr" class="bF1SaleCard_eErr"></p>
            </div>
        <? else: ?>
            <p class="font11"><a href="<?= $page->url('cart') ?>">Введите серийный номер карты «Под защитой F1» для скидки на услуги</a></p>
        <? endif ?>
    </div>
    <div class="line mt32 pb30"></div>
</div>

<? endif ?>
