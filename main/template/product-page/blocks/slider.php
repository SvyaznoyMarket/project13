<?php

use \Model\Product\Entity as Product;
use Model\Product\RichRelevanceProduct;

/**
 * @param \Helper\TemplateHelper $helper
 * @param Product[]|RichRelevanceProduct[] $products
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
    $sender += ['name' => null, 'method' => null, 'position' => null, 'from' => null, 'items' => []];

    unset($sender['items']);

    $sliderId = 'slider-' . uniqid();

    $id = 'slider-' . md5(json_encode([$url, $sender, $type]));

    // слайдер товаров для слайдера с аксессуарами применяем модификатор goods-slider--5items
    ?>

    <div class="goods-slider js-slider-2 clearfix <? if ((bool)$categories): ?> goods-slider--width goods-slider--5items<? endif ?><? if ($url && !(bool)$products): ?> <? endif ?><? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?> <?= $class ?>"
        id="<?= $id ?>"
        data-position="<?= $sender['position'] ?>"
        data-slider="<?= $helper->json([
            'limit'  => $limit,
            'url'    => $url,
            'type'   => $type,
            'sender' => $sender,
            'sender2' => $sender2,
        ]) ?>" >

        <? if ($title): ?>
            <p class="product-section__h3"><?= $title ?></p>
        <? endif ?>

        <? if ((bool)$categories): ?>
            <div class="product-accessoires">
                <ul class="product-accessoires-list">
                    <? $i = 0; foreach ($categories as $category): ?>
                        <li id="<?= $sliderId . '-category-' . $category->getId() ?>"
                            class="product-accessoires-list-item js-product-accessoires-category <? if (0 == $i): ?> mActive<? endif ?>"
                            data-product="<?= $category->getId() ? 'self' : 'all' ?>">
                            <span class="product-accessoires-list-item__name"><?= $category->getName() ?></span>
                        </li>
                        <? $i++; endforeach ?>
                </ul>
            </div>
        <? endif ?>

        <div class="goods-slider__inn">
            <ul class="goods-slider-list clearfix">

                <? foreach ($products as $index => $product):

                    /** @var $product Product|RichRelevanceProduct */

                    $elementId = 'productLink-' . $product->getId() . '-' . md5(json_encode([$sender]));    // для tealeaf

                    $urlParams = [];
                    if ($sender['name']) {
                        $urlParams['sender'] = $sender;
                    }

                    if ($sender2) {
                        $urlParams['sender2'] = $sender2;
                    }

                    $link = $helper->url('product', ['productPath' => $product->getPath()]) . '#' . http_build_query($urlParams);

                    $onclick = null;
                    if ($product instanceof \Model\Product\RichRelevanceProduct) {
                        $onclick = $product->getOnClickTag();
                    }

                    $category = $product->getParentCategory() ? $product->getParentCategory() : null;
                    ?>

                    <li class="goods-slider-list__i"
                        data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>"
                        data-product="<?= $helper->json([
                            'article'  => $product->getArticle(),
                            'name'     => $product->getName(),
                            'isUpsale' => $product->getIsUpsale(),
                        ]) ?>" >
                        <div class="goods-slider-list__i-inner">
                            <? if ($product->getLabel()) : ?>
                                <img class="sticker-img" src="<?= $product->getLabel()->getImageUrl() ?>" alt="<?= $product->getLabel()->getName() ?>">
                            <? endif ?>

                            <a id="<?= $elementId ?>" class="goods-slider-list__link" href="<?= $link ?>" <?= $onclick ? : null ?> target="_blank">

                            <span class="goods-slider-list__action">
                                <img class="goods-slider-list__img" src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>">
                            </span>

                                <span class="goods-slider-list__name"><?= $helper->escape($product->getName()) ?></span>
                            </a>

                            <div class="goods-slider-list__price-old">
                                <? if ($product->getPriceOld()) : ?>
                                    <span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
                                <? endif ?>
                            </div>

                            <div class="goods-slider-list__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>

                            <?= $helper->render('cart/__button-product', [
                                'product'        => $product,
                                'onClick'        => null,
                                'sender'         => $sender,
                                'noUpdate'       => true,
                                'location'       => 'slider',
                                'sender2'       => $sender2,
                            ]) // Кнопка купить ?>

                            <!--                <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>-->
                        </div>

                    </li>

                <? endforeach ?>

            </ul>
        </div>

        <div class="goods-slider__btn goods-slider__btn--prev disabled"></div>
        <div class="goods-slider__btn goods-slider__btn--next disabled"></div>

    </div>
    <!--/ слайдер товаров -->


<? }; return $f;