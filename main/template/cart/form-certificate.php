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

<? if (\App::config()->f1Certificate['enabled'] || \App::config()->coupon['enabled']): ?>

<div class="clear"></div>

<div class="bF1SaleCard">
    <div class="">
        <h3 class="bF1SaleCard_eTitle ">Скидки</h3>

        <? if (\App::config()->f1Certificate['enabled'] && (bool)$user->getCart()->getCertificates()): ?>
            <div class="bF1SaleCard_eComplete mGold ml90">
                <span class="font14">Для заказа действует скидка по программе «Под защитой F1» <a class="bF1SaleCard_eDel button whitelink ml5 mInlineBlock" href="#" data-url="<?= $page->url('cart.certificate.delete') ?>">Удалить</a></span>
            </div>
        <? endif ?>

        <? if (\App::config()->coupon['enabled'] && (bool)$user->getCart()->getCoupons()): ?>
            <? foreach ($user->getCart()->getCoupons() as $coupon): ?>
            <div class="bF1SaleCard_eComplete mGold ml90 <? if ($coupon->getError()): ?> mError<? else: ?><? endif ?>">
                <? if ($coupon->getError()): ?>
                <span class="font14">
                    Невозможно применить скидку «<?= $coupon->getName() ?>»: <?= \App::config()->debug ? $coupon->getError()->getMessage() : \Model\Cart\Coupon\Entity::getErrorMessage($coupon->getError()->getCode()) ?>
                </span>
                <? else: ?>
                <span class="font14">
                    Для заказа действует скидка «<?= $coupon->getName() ?>»
                </span>
                <? endif ?>
                <a class="bF1SaleCard_eDel button whitelink ml5 mInlineBlock" href="#" data-url="<?= $page->url('cart.coupon.delete') ?>">Удалить</a>
            </div>
            <? endforeach ?>
        <? endif ?>

        <div class="bF1SaleCard_eForm pt20 ml90 <? if ($user->getCart()->hasServices()): ?> m2Coupon<? endif ?>">
            <div class="bF1SaleCard_eRadiogroup">
                <label class="bF1SaleCard_eLabel"><input id="cartCertificateAll" class="bF1SaleCard_eRadio" name="coupon" type="radio" checked="checked" data-url="<?= $page->url('cart.coupon.apply') ?>" /> Код скидки на товары</label>
                <label class="bF1SaleCard_eLabel"><input id="cartCertificateF1" class="bF1SaleCard_eRadio" name="coupon" type="radio" data-url="<?= $page->url('cart.certificate.apply') ?>" /> Скидки на услуги F1 по карте «Под защитой F1»</label>
            </div>
            <input id="F1SaleCard_number" class="mr20 width370" type="text" placeholder="Код скидки"/><input id="F1SaleCard_btn" class="yellowbutton button" type="button" value="Применить" />
            <p id="bF1SaleCard_eErr" class="bF1SaleCard_eErr"></p>
        </div>

    </div>
    <div class="line mt32 pb30"></div>
</div>

<? endif ?>
