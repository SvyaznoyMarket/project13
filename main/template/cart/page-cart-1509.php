<?php
/**
 * @var $page     Templating\HtmlLayout
 * @var $user     \Session\User
 * @var $orderUrl string
*/

$cart = $user->getCart();
$helper = new \Helper\TemplateHelper();
$recommendationsSender = [
    'name' => 'rich'
]
?>

<div class="order__wrap order-page">
    <div class="jsKnockoutCart order-cart" data-bind="visible: cart().sum() == 0" style="display: none">
        <?= $page->render('cart/_cart-empty') ?>
    </div>

    <div class="jsKnockoutCart order-cart" data-bind="visible: cart().sum() > 0" style="display: none">

        <?//= $page->render('cart/partner/_adfox') ?>

        <!-- ko foreach: cart().products() -->
            <?= $page->render('cart/_cart-item-1509') ?>
        <!-- /ko -->

        <?= $page->render('cart/_cart-total-1509') ?>
    </div>

        <div class="order-cart__btn mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">
            <a href="<?= (@$orderUrl ?: $page->url('orderV3')) ?>" class="btn-type btn-type--buy btn-type--order" data-bind="visible: !isMinOrderSumVisible()">Оформить заказ</a>
        </div>
        <div class="order-cart__back mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none"><a href="<?= $backlink ?>"><span class="order-cart__back-txt">Вернуться к покупкам</span></a></div>
        <div class="cart-alert jsKnockoutCart" data-bind="visible: isMinOrderSumVisible()" style="display: none;">
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
                        'type'      => 'cart_page.rr3',
                        'sender'    => $recommendationsSender,
                        'products'  => [],
                        'url'       => $page->url('cart.recommended', [
                            'types'  => ['cart_page.rr1', 'cart_page.rr3'],
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
                    'type'      => 'cart_page.rr1',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['cart_page.rr1', 'cart_page.rr3'],
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
                    'type'      => 'cart_page.rr1',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['cart_page.rr1', 'cart_page.rr2'],
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
                    'type'      => 'cart_page.rr2',
                    'sender'    => $recommendationsSender,
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['cart_page.rr1', 'cart_page.rr2'],
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
</div>
<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>