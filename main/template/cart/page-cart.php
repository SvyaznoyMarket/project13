<?php
/**
 * @var $page Templating\HtmlLayout
 * @var $user \Session\User
*/

$cart = $user->getCart();
$helper = new \Helper\TemplateHelper();

// АБ-тест рекомендаций
$test = \App::abTest()->getTest('recommended_product');
$isNewRecommendation =
    $test->getEnabled()
    && $test->getChosenCase()
    && ('new_recommendation' == $test->getChosenCase()->getKey())
;

?>

<div class="jsKnockoutCart" data-bind="visible: !isUpdated()">
    <?= $page->render('cart/_spinner') ?>
</div>

<div class="jsKnockoutCart" data-bind="visible: isUpdated() && cartSum() == 0" style="display: none">
    <?= $page->render('cart/_cart-empty') ?>
</div>

<div class="jsKnockoutCart" data-bind="visible: isUpdated() && cartSum() > 0" style="display: none">

    <?= $page->render('cart/partner/_adfox') ?>

    <!-- ko foreach: cart -->
    <?= $page->render('cart/_cart-item') ?>
    <!-- /ko -->

    <div class="basketLine clearfix">

        <?= $page->render('cart/ab-self-delivery/_infoblock', ['cart' => $cart]) ?>

        <?= $page->render('cart/_cart-total') ?>

    </div>

</div>

<? if (!$isNewRecommendation): ?>
    <?= $page->render('cart/ab-self-delivery/_recommendSlider') ?>
<? endif ?>

<? if ($cart->isEmpty()): ?>
    <div class="basketLine">
    <?= $helper->render('product/__slider', [
        'type'      => 'main',
        'products'  => [],
        'url'       => $page->url('cart.recommended', [
            'sender' => [
                'position' => 'Basket',
            ],
        ]),
    ]) ?>
    </div>
    <div class="basketLine">
    <?= $helper->render('product/__slider', [
        'type'      => 'alsoBought',
        'products'  => [],
        'url'       => $page->url('cart.recommended', [
            'sender' => [
                'position' => 'Basket',
            ],
        ]),
    ]) ?>
    </div>
    <div class="cart--ep"><a href="/enterprize" title=""><img src="/css/bEmptyCart/img/ep.jpg" alt="" /></a></div>
<? endif ?>

    <div class="clear"></div>

<div class="backShop fl mNoPrint jsKnockoutCart" data-bind="visible: isUpdated() && cartSum() > 0" style="display: none">&lt; <a class="underline" href="<?= $backlink ?>">Вернуться к покупкам</a></div>

<div class="basketBuy mNoPrint jsKnockoutCart" data-bind="visible: isUpdated() && cartSum() > 0" style="display: none">
    <a href="<?= $page->url('order') ?>" class="bBigOrangeButton">Оформить заказ</a>
</div>

<div class="clear"></div>

<? if ($isNewRecommendation && \App::config()->product['pullRecommendation']): ?>

    <? if (!$cart->isEmpty()): ?>
    <div class="basketLine">
        <?= $helper->render('product/__slider', [
            'type'      => 'alsoBought',
            'products'  => [],
            'url'       => $page->url('cart.recommended', [
                'sender' => [
                    'position' => 'Basket',
                ],
            ]),
        ]) ?>
        </div>
    <? endif ?>
<? endif ?>

<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->render('cart/partner/_mixmarket') ?>
    <?= $page->render('cart/partner/_kiss', ['cart' => $cart]) ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>