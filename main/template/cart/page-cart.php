<?php
/**
 * @var $page     Templating\HtmlLayout
 * @var $user     \Session\User
 * @var $orderUrl string
*/

$cart = $user->getCart();
$helper = new \Helper\TemplateHelper();
$isNewProductPage = \App::abTest()->isNewProductPage();
?>

<? /*
<div class="jsKnockoutCart">
    <?= $page->render('cart/_spinner') ?>
</div>
*/ ?>
<div class="order__wrap order-page">
    <div class="pagehead">
        <h1>Корзина</h1>
        <div class="clear"></div>
    </div>
    <div class="jsKnockoutCart order-cart" data-bind="visible: cart().sum() == 0" style="display: none">
        <?= $page->render('cart/_cart-empty') ?>
    </div>

    <div class="jsKnockoutCart order-cart" data-bind="visible: cart().sum() > 0" style="display: none">

        <?//= $page->render('cart/partner/_adfox') ?>

        <!-- ko foreach: cart().products() -->
            <?= //$page->render('cart/_cart-item')
                $page->render('cart/_cart-item-new')
            ?>
        <!-- /ko -->

        <!--<div class="basketLine clearfix">-->

            <?= $page->render('cart/ab-self-delivery/_infoblock', ['cart' => $cart]) ?>

            <?= $page->render('cart/_cart-total-new') ?>

        <!--</div>-->

    </div>

    <!--<div class="backShop fl mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">&lt; <a class="underline" href="<?/*= $backlink */?>">Вернуться к покупкам</a></div>-->


        <div class="order-cart__btn mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none">
            <a href="<?= (@$orderUrl ?: $page->url('order')) ?>" class="btn-type btn-type--buy btn-type--order" data-bind="visible: !isMinOrderSumVisible()">Оформить заказ</a>
        </div>
        <div class="order-cart__back mNoPrint jsKnockoutCart" data-bind="visible: cart().sum() > 0" style="display: none"><a href="<?= $backlink ?>"><span class="order-cart__back-txt">Вернуться к покупкам</span></a></div>
        <div class="cart-alert jsKnockoutCart" data-bind="visible: isMinOrderSumVisible()" style="display: none;">
            <span class="cart-alert__info">До оформления заказа осталось</span>
            <span class="cart-alert__remain-sum"><span data-bind="text: minOrderSum - cart().sum()"><?= \App::config()->minOrderSum ?></span>&thinsp;<span class="rubl">p</span></span>
        </div>

    <div class="clear"></div>

    <? if (!$cart->count()): // жуткий костыль SITE-5289 ?>
        <div id="js-cart-firstRecommendation" style="display: none;">
            <? $page->startEscape()?>
            <div class="basketLine">
                <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
                    'type'      => 'alsoBought',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]) ?>
            </div>
            <? $page->endEscape() ?>
        </div>

        <div class="basketLine">
        <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
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
        <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
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


    <? if (\App::config()->product['pullRecommendation'] && $cart->count()): ?>
        <div class="basketLine">
            <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
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
</div>
<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>