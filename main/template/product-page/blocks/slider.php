<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity[] $products
 * @param null $title
 * @param \Model\Product\Category\Entity[] $categories
 * @param null $class
 * @param null $count
 * @param null $limit
 * @param null $url
 * @param null $type
 * @param string $namePosition
 * @param array $sender
 * @param bool $isCompact
 * @param bool $showPageCounter         Показывать "Страница n из N"
 * @param int  $rowsCount               Количество строк в слайдере
 * @param string|null $containerStyle
 * @param string $sender2
 */
$f = function (
    \Helper\TemplateHelper $helper,
    array $products,
    $title = null,
    array $categories = [],
    $class = null,
    $count = null,
    $limit = null,
    $url = null,
    $type = null,
    $namePosition = null,
    array $sender = [],
    $isCompact = false,
    $showPageCounter = false,
    $rowsCount = 1,
    $containerStyle = '', // вот это хардкод
    $sender2 = ''
) {
    if (null === $namePosition) {
        $namePosition = 'bottom';
    }

    $sender += ['name' => null, 'method' => null, 'position' => null, 'from' => null, 'items' => []];

    $isRetailrocketRecommendation = ('retailrocket' == $sender['name']);
    $retailrocketMethod = $sender['method'];
    $retailrocketIds = (array)$sender['items'];
    unset($sender['items']);

    $sliderId = 'slider-' . uniqid();

    $id = 'slider-' . md5(json_encode([$url, $sender, $type]));
    $products = array_filter($products, function($product) { return $product instanceof \Model\Product\Entity; });

    // открытие товаров в новом окне
    $linkTarget = \App::abTest()->isNewWindow() ? ' target="_blank" ' : '';

    // слайдер товаров для слайдера с аксессуарами применяем модификатор goods-slider--5items
    ?>


    <div class="goods-slider js-slider-2 clearfix <? if ((bool)$categories): ?> goods-slider--width goods-slider--5items<? endif ?><? if ($url && !(bool)$products): ?> <? endif ?><? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?>"
        id="<?= $id ?>"
        data-position="<?= $sender['position'] ?>"
        data-slider="<?= $helper->json([
            'count'  => $count,
            'limit'  => $limit,
            'url'    => $url,
            'type'   => $type,
            'sender' => $sender,
            'sender2' => $sender2,
        ]) ?>" >

        <? if ((bool)$categories): ?>
            <div class="product-accessoires">
                <ul class="product-accessoires-list">
                    <? $i = 0; foreach ($categories as $category): ?>
                        <li id="<?= $sliderId . '-category-' . $category->getId() ?>" class="product-accessoires-list-item js-product-accessoires <? if (0 == $i): ?> mActive<? endif ?>" data-product="<?= $category->getId() ? 'self' : 'all' ?>">
                            <span class="product-accessoires-list-item__name"><?= $category->getName() ?></span>
                        </li>
                        <? $i++; endforeach ?>
                </ul>
            </div>
        <? endif ?>

        <div class="goods-slider__inn">
            <ul class="goods-slider-list clearfix">

                <? foreach ($products as $index => $product):

                    /** @var $product \Model\Product\Entity */

                    $elementId = 'productLink-' . $product->getId() . '-' . md5(json_encode([$sender]));    // для tealeaf

                    $urlParams = [];
                    if ($sender['name']) {
                        $urlParams['sender'] = $sender;
                    }
                    if ('retailrocket' == $sender['name']) {
                        $urlParams['from'] = 'cart_rec';
                    }

                    if ($sender2) {
                        $urlParams['sender2'] = $sender2;
                    }

                    $link = $helper->url(
                        'product',
                        array_merge(
                            ['productPath' => $product->getPath()],
                            $urlParams
                        ));

                    // Retailrocket
                    $isRetailrocketProduct = in_array($product->getId(), $retailrocketIds);
                    $linkClickJS = null;
                    $addToCartJS = null;
                    if ($isRetailrocketRecommendation && !empty($retailrocketMethod) && $isRetailrocketProduct) {
                        // Клик по гиперссылке с товарной рекомендацией
                        $linkClickJS = "try{rrApi.recomMouseDown({$product->getId()}, {methodName: '{$retailrocketMethod}'})}catch(e){}";

                        // Добавление товара в корзину из блока с рекомендациями
                        $addToCartJS = "try{rrApi.recomAddToCart({$product->getId()}, {methodName: '{$retailrocketMethod}'})}catch(e){}";
                    }

                    $category = $product->getParentCategory() ? $product->getParentCategory() : null;
                    ?>

                    <li class="goods-slider-list__i" data-category="" data-product="">

                        <? if ($product->getLabel()) : ?>
                            <img class="slideItem_stick" src="<?= $product->getLabel()->getImageUrl() ?>" alt="<?= $product->getLabel()->getName() ?>">
                        <? endif ?>

                        <a id="<?= $elementId ?>" class="goods-slider-list__link" href="<?= $link ?>" target="_blank">

                    <span class="goods-slider-list__action">
                        <img class="goods-slider-list__img" src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>">
                    </span>

                            <span class="goods-slider-list__name"><?= $product->getName() ?></span>
                        </a>

                        <div class="goods-slider-list__price-old">
                            <? if ($product->getPriceOld()) : ?>
                                <span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
                            <? endif ?>
                        </div>

                        <div class="goods-slider-list__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>

                        <?= $helper->render('cart/__button-product', [
                            'product'        => $product,
                            'onClick'        => $addToCartJS ? $addToCartJS : null,
                            'isRetailRocket' => $isRetailrocketProduct, // TODO: удалить
                            'sender'         => $sender,
                            'noUpdate'       => true,
                            'location'       => 'slider',
                            'sender2'       => $sender2,
                        ]) // Кнопка купить ?>

                        <!--                <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>-->
                    </li>

                <? endforeach ?>

            </ul>
        </div>

        <div class="goods-slider__btn goods-slider__btn--prev disabled"></div>
        <div class="goods-slider__btn goods-slider__btn--next disabled"></div>

    </div>
    <!--/ слайдер товаров -->


<? }; return $f;