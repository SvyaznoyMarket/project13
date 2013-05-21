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

<div class="bF1SaleCard<? if (!$user->getCart()->hasServices()): ?> hidden<? endif ?>">
    <div class="pl35">
        <h3 class="bF1SaleCard_eTitle ">Скидка по карте «Под защитой F1»</h3>
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
            <div class="bF1SaleCard_eForm">
                <input type="radio" data-url="<?= $page->url('cart.certificate.apply') ?>" /> Код скидки на товары
                <input type="radio" data-url="<?= $page->url('cart.coupon.apply') ?>" /> Скидки на услуги F1 по карте «Под защитой F1»
                <input id="F1SaleCard_number" class="mr20 width370" type="text"/><input id="F1SaleCard_btn" class="yellowbutton button" type="button" value="Применить" />
                <p id="bF1SaleCard_eErr" class="bF1SaleCard_eErr"></p>
            </div>
        <? else: ?>
            <p class="font11"><a href="<?= $page->url('cart') ?>">Введите серийный номер карты «Под защитой F1» для скидки на услуги</a></p>
        <? endif ?>
    </div>
    <div class="line mt32 pb30"></div>
</div>

<? endif ?>