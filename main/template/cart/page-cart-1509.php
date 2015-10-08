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
            <?= $page->render('cart/_cart-item-1509') ?>
        <!-- /ko -->

        <!--<div class="basketLine clearfix">-->

            <?= $page->render('cart/ab-self-delivery/_infoblock', ['cart' => $cart]) ?>

            <?= $page->render('cart/_cart-total-1509') ?>

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

    <div class="s-sales">
        <!-- Акция завершина -->
        <div class="s-sales-noty">
            <p class="s-sales-noty__paragraph">Акция завершена. Однако, у нас есть для вас еще много интересных предложений!</p>
        </div>

        <!-- Сетка скидочных категорий -->
        <div class="s-sales-grid">
            <!--
                Строка с тремя ячейками, выста каждой ячейки 220 пиксел
                Модификатор grid-3cell cell-h-220
             -->
            <div class="s-sales-grid__row grid-3cell cell-h-220">
                <div class="s-sales-grid__cell">
                    <a class="s-sales-grid__link" href="">
                        <img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

                        <span class="s-sales-grid-desc">
                            <span class="s-sales-grid-desc__value">-70%</span>
                            <span class="s-sales-grid-desc__title">
                                <span class="s-sales-grid-desc__title-name">Путешествия</span>
                                <span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
                            </span>
                        </span>
                    </a>
                </div>

                <div class="s-sales-grid__cell">
                    <a class="s-sales-grid__link" href="">
                        <img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

                        <span class="s-sales-grid-desc">
                            <span class="s-sales-grid-desc__value">-70%</span>
                            <span class="s-sales-grid-desc__title">
                                <span class="s-sales-grid-desc__title-name">Путешествия</span>
                                <span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
                            </span>
                        </span>
                    </a>
                </div>

                <div class="s-sales-grid__cell">
                    <a class="s-sales-grid__link" href="">
                        <img src="http://img0.joyreactor.cc/pics/post/full/%D0%BA%D0%BE%D1%82%D0%B8%D0%BA%D0%B8-%D0%B6%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D1%8C-wallpaper-%D0%B1%D0%B0%D1%8F%D0%BD-885065.jpeg" alt="" class="s-sales-grid__img">

                        <span class="s-sales-grid-desc">
                            <span class="s-sales-grid-desc__value">-70%</span>
                            <span class="s-sales-grid-desc__title">
                                <span class="s-sales-grid-desc__title-name">Путешествия</span>
                                <span class="s-sales-grid-desc__title-duration">Конец акции 22.09.2015 00:00</span>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
            <!--END Конец строки -->
        </div>

        <div class="button-container">
            <a href="" class="button button_action button_size-l">Посмотреть текущие акции</a>
        </div>
    </div>


    <? if (\App::config()->product['pullRecommendation']): ?>
        <? if ($cart->count()): ?>
            <div class="basketLine">
                <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
                    'type'      => 'alsoBought',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['alsoBought', 'personal'],
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]) ?>
            </div>

            <div class="basketLine">
                <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
                    'type'      => 'personal',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['alsoBought', 'personal'],
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]) ?>
            </div>
        <? else: ?>
            <? /* Жуткий костыль SITE-5289 */ ?>
            <div id="js-cart-firstRecommendation" style="display: none;">
                <? $page->startEscape()?>
                <div class="basketLine">
                    <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
                        'type'      => 'popular',
                        'products'  => [],
                        'url'       => $page->url('cart.recommended', [
                            'types'  => ['personal', 'popular'],
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
                    'type'      => 'personal',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['personal', 'popular'],
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]) ?>
            </div>

            <div class="basketLine">
                <?= $helper->render($isNewProductPage ? 'product-page/blocks/slider' : 'product/__slider', [
                    'type'      => 'popular',
                    'products'  => [],
                    'url'       => $page->url('cart.recommended', [
                        'types'  => ['personal', 'popular'],
                        'sender' => [
                            'position' => 'Basket',
                        ],
                    ]),
                ]) ?>
            </div>

            <div class="cart--ep"><a href="/enterprize" title=""><img src="/css/bEmptyCart/img/ep.jpg" alt="" /></a></div>
        <? endif ?>

        <div class="clear"></div>
    <? endif ?>
</div>
<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('cart/partner/_cityads') ?>
<? endif ?>