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
            <div class="bF1SaleCard_eComplete mSertificate mGold ml90">
                <span class="font14">Для заказа действует скидка по программе «Под защитой F1» <a class="bF1SaleCard_eDel button whitelink ml5 mInlineBlock" href="#" data-url="<?= $page->url('cart.certificate.delete') ?>">Удалить</a></span>
            </div>
        <? endif ?>

        <? if (\App::config()->coupon['enabled'] && (bool)$user->getCart()->getCoupons()): ?>
            <? foreach ($user->getCart()->getCoupons() as $coupon): ?>
            <div class="bF1SaleCard_eComplete mCoupon mGold ml90 <? if ($coupon->getError()): ?> mError<? else: ?><? endif ?>">
                <? if ($coupon->getError()): ?>
                <span class="font14">
                    Невозможно применить скидку<?= $coupon->getName() ? (sprintf(' «%s»', $coupon->getName())) : '' ?>: <?= \Model\Cart\Coupon\Entity::getErrorMessage($coupon->getError()->getCode()) . (\App::config()->debug ? sprintf('. <span class="gray">%s</span>', $coupon->getError()->getMessage()) : '') ?>
                </span>
                <? else: ?>
                <span class="font14">
                    Для заказа действует скидка<?= $coupon->getName() ? (sprintf(' «%s»', $coupon->getName())) : '' ?>
                </span>
                <? endif ?>
                
                <a class="bF1SaleCard_eDel button whitelink ml5 mInlineBlock" href="#" data-url="<?= $page->url('cart.coupon.delete') ?>">Удалить</a>
                <? if ($coupon->getDiscountSum()): ?>
                    <div class="font14 fr"><span class="price">-<?= $page->helper->formatPrice($coupon->getDiscountSum()) ?> </span><span class="rubl">p</span></div>
                <? endif ?>
            </div>
            <? endforeach ?>
            <a class="ml90 underline bold db pt20" href="/coupons">Условия применения купонов</a>
        <? endif ?>

        <? if (\App::config()->blackcard['enabled'] && (bool)$user->getCart()->getBlackcards()): ?>
            <? foreach ($user->getCart()->getBlackcards() as $blackcard): ?>
                <div class="bF1SaleCard_eComplete mCoupon mGold ml90 <? if ($blackcard->getError()): ?> mError<? else: ?><? endif ?>">
                    <? if ($blackcard->getError()): ?>
                    <span class="font14">
                        Невозможно применить скидку<?= $blackcard->getName() ? (sprintf(' «%s»', $blackcard->getName())) : '' ?>: <?= \Model\Cart\Blackcard\Entity::getErrorMessage($blackcard->getError()->getCode()) . (\App::config()->debug ? sprintf('. <span class="gray">%s</span>', $blackcard->getError()->getMessage()) : '') ?>
                    </span>
                    <? else: ?>
                    <span class="font14">
                        Для заказа действует скидка<?= $blackcard->getName() ? (sprintf(' «%s»', $blackcard->getName())) : '' ?>
                    </span>
                    <? endif ?>

                    <a class="bF1SaleCard_eDel button whitelink ml5 mInlineBlock" href="#" data-url="<?= $page->url('cart.blackcard.delete') ?>">Удалить</a>
                    <? if ($blackcard->getDiscountSum()): ?>
                        <div class="font14 fr"><span class="price">-<?= $page->helper->formatPrice($blackcard->getDiscountSum()) ?> </span><span class="rubl">p</span></div>
                    <? endif ?>
                </div>
            <? endforeach ?>
            <a class="ml90 underline bold db pt20" href="/coupons">Условия применения купонов</a>
        <? endif ?>

        <div class="bF1SaleCard_eForm pt20 ml90 m2Coupon" style="display:block">
            <div class="bF1SaleCard_eRadiogroup">
                <label class="bF1SaleCard_eLabel"><input id="cartCertificateAll" class="bF1SaleCard_eRadio" name="coupon" type="radio" checked="checked" data-url="<?= $page->url('cart.coupon.apply') ?>" /> Код скидки на товары</label>
                <!--<label class="bF1SaleCard_eLabel"><input id="cartCertificateF1" class="bF1SaleCard_eRadio" name="coupon" type="radio" data-url="<?//= $page->url('cart.certificate.apply') ?>" /> Скидки на услуги F1 по карте «Под защитой F1»</label>-->
                <? if (\App::config()->blackcard['enabled']): ?>
                    <label class="bF1SaleCard_eLabel"><input id="cartCertificateBlack" class="bF1SaleCard_eRadio" name="coupon" type="radio" data-url="<?= $page->url('cart.blackcard.apply') ?>" /> «Черная карта»</label>
                <? endif ?>
            </div>
            <input id="F1SaleCard_number" class="mr20 width370" type="text" placeholder="Код скидки"/><input id="F1SaleCard_btn" class="yellowbutton button" type="button" value="Применить" />
            <p id="bF1SaleCard_eErr" class="bF1SaleCard_eErr"></p>
        </div>

    </div>
    <div class="line mt32 pb30"></div>
</div>

<? endif ?>


<script type="text/javascript">
$(document).ready(function() {
    //setTimeout(function() { $('.bF1SaleCard_eForm, .bF1SaleCard_eRadiogroup').show('medium'); }, 2000);
});
</script>