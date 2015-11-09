<?php
/**
 * @var $page Templating\HtmlLayout
 * @var $user \Session\User
 */

$cart = $user->getCart();
$helper = new \Helper\TemplateHelper();
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

        <?= $page->render('cart/ab-self-delivery/_infoblock', ['cart' => $cart]) ?>

        <?= $page->render('cart/_cart-total') ?>

    </div>

</div>

<div class="backShop fl mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">&lt; <a class="underline" href="<?= $backlink ?>">Вернуться к покупкам</a></div>

<div class="basketBuy mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">
    <a href="<?= $page->url('order') ?>" class="bBigOrangeButton" data-bind="visible: !isMinOrderSumVisible()">Оформить заказ</a>
</div>

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
                'type'      => 'alsoBought',
                'products'  => [],
                'url'       => $page->url('cart.recommended', [
                    'types'  => ['alsoBought'],
                    'sender' => [
                        'position' => 'Basket',
                    ],
                ]),
            ]
        ) ?>
        </div>
    <? else: ?>
        <? /* Жуткий костыль SITE-5289 */ ?>
        <div id="js-cart-firstRecommendation" style="display: none;">
            <? $page->startEscape()?>
            <div class="basketLine">
            <?= $helper->render(
                'product-page/blocks/slider',
                [
                    'type'      => 'popular',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['personal', 'popular'],
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]
            ) ?>
            </div>
            <? $page->endEscape() ?>
        </div>

        <div class="basketLine">
        <?= $helper->render(
            'product-page/blocks/slider',
            [
                'type'      => 'personal',
                'products'  => [],
                'url'       => $page->url('cart.recommended', [
                    'types'  => ['personal', 'popular'],
                    'sender' => [
                        'position' => 'Basket',
                    ],
                ]),
            ]
        ) ?>
        </div>

        <div class="basketLine">
        <?= $helper->render(
            'product-page/blocks/slider',
            [
                'type'      => 'popular',
                'products'  => [],
                'url'       => $page->url('cart.recommended', [
                    'types'  => ['personal', 'popular'],
                    'sender' => [
                        'position' => 'Basket',
                    ],
                ]),
            ]
        ) ?>
        </div>

        <div class="cart--ep"><a href="/enterprize" title=""><img src="/css/bEmptyCart/img/ep.jpg" alt="" /></a></div>
    <? endif ?>

    <div class="clear"></div>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>