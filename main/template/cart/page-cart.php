<?php
/**
 * @var $page Templating\HtmlLayout
 * @var $user \Session\User
 */

$cart = $user->getCart();
$helper = new \Helper\TemplateHelper();
$isRich = \App::abTest()->isRichRelRecommendations();
$recommendationsSender = [
    'name' => $isRich ? 'rich' : 'retailrocket'
]
?>

<? /*
<div class="jsKnockoutCart">
    <?= $page->render('cart/_spinner') ?>
</div>
*/ ?>

<div class="jsKnockoutCart" data-bind="visible: cart().sum() == 0" style="display: none">
    <?= $page->render('cart/_cart-empty') ?>
</div>

<div class="jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">

    <?= $page->render('cart/partner/_adfox') ?>

    <!-- ko foreach: cart().products() -->
    <?= $page->render('cart/_cart-item') ?>
    <!-- /ko -->

    <div class="basketLine clearfix">

        <div class="basketLine__min-sum" data-bind="visible: isMinOrderSumVisible()">
            <span class="basketLine__min-sum-desc">Минимальная стоимость заказа 990 <span class="rubl">p</span></span>
        </div>

        <?= $page->render('cart/ab-self-delivery/_infoblock', ['cart' => $cart]) ?>

        <?= $page->render('cart/_cart-total') ?>

    </div>

</div>

<div class="backShop fl mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">&lt; <a class="underline" href="<?= $backlink ?>">Вернуться к покупкам</a></div>

<div class="basketBuy mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">
    <a href="<?= $page->url('orderV3') ?>" class="bBigOrangeButton" data-bind="visible: !isMinOrderSumVisible()">Оформить заказ</a>
</div>

<div class="cart-alert cart-alert_mr jsKnockoutCart" data-bind="visible: isMinOrderSumVisible()" style="display: none;">
    <span class="cart-alert__info">До оформления заказа осталось</span>
    <span class="cart-alert__remain-sum"><span data-bind="text: minOrderSum - cart().sum()"><?= \App::config()->minOrderSum ?></span>&thinsp;<span class="rubl">p</span></span>
</div>

<div class="clear"></div>

<?= $page->render('cart/_cart-sales', ['sales' => $sales]) ?>

<? if (\App::config()->product['pullRecommendation']): ?>
    <? if ($cart->count()): ?>
        <div class="basketLine">
            <?= $helper->render(
                'product-page/blocks/slider',
                [
                    'type'      => $isRich ? 'cart_page.rr3' : 'alsoBought',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => $isRich ? ['cart_page.rr1', 'cart_page.rr3'] : ['alsoBought', 'personal'],
                        'sender' => [
                                'position' => 'Basket',
                            ] + $recommendationsSender,
                    ]),
                ]
            ) ?>
        </div>
    <? else: ?>
        <div class="basketLine">
            <?= $helper->render(
                'product-page/blocks/slider',
                [
                    'type'      => $isRich ? 'cart_page.rr1' : 'personal',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => $isRich ? ['cart_page.rr1', 'cart_page.rr2'] : ['personal', 'popular'],
                        'sender' => [
                                'position' => 'Basket',
                            ] + $recommendationsSender,
                    ]),
                ]
            ) ?>
        </div>

        <div class="basketLine">
            <?= $helper->render(
                'product-page/blocks/slider',
                [
                    'type'      => $isRich ? 'cart_page.rr2' : 'popular',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => $isRich ? ['cart_page.rr1', 'cart_page.rr2'] : ['personal', 'popular'],
                        'sender' => [
                                'position' => 'Basket',
                            ] + $recommendationsSender,
                    ]),
                ]
            ) ?>
        </div>
    <? endif ?>

    <div class="clear"></div>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>