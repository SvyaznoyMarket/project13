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

<div class="clear"></div>

<div class="bF1SaleCard<? if (!$user->getCart()->hasServices()): ?> hidden<? endif ?>">
    <div class="pl35">
        <h3 class="bF1SaleCard_eTitle ">Скидка по карте «Под защитой F1»</h3>
        <? if ((bool)$user->getCart()->getCertificates()): ?>
            <div class="bF1SaleCard_eComplete mGold">
                <p class="font14">Для заказа действует скидка по программе «Под защитой F1»</p>
            </div>
        <? elseif ($isForm): ?>
            <div class="bF1SaleCard_eForm">
                <p class="font11">Введите серийный номер карты «Под защитой F1» для скидки на услуги:</p>
                <input id="F1SaleCard_number" class="mr20 width370" type="text"/><input id="F1SaleCard_btn" data-url="<?= $page->url('cart.certificate.apply') ?>" class="yellowbutton button" type="button" value="Получить скидку"/>
                <p id="bF1SaleCard_eErr" class="bF1SaleCard_eErr"></p>
            </div>
        <? else: ?>
            <p class="font11"><a href="<?= $page->url('cart') ?>">Введите серийный номер карты «Под защитой F1» для скидки на услуги</a></p>
        <? endif ?>
    </div>
    <div class="line mt32 pb30"></div>
</div>