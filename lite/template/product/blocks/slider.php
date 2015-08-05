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

    $retailrocketIds = (array)$sender['items'];
    unset($sender['items']);

    $sliderId = 'slider-' . uniqid();

    $products = array_filter($products, function($product) { return $product instanceof \Model\Product\Entity; });

    $slickConfig = [
        'slidesToShow' => 6,
        'slidesToScroll' => 6,
//        'lazyLoad'  => 'ondemand',
        'dots'      => false,
        'infinite'  => false,
        'nextArrow' => '.js-goods-slider-btn-next',
        'prevArrow' => '.js-goods-slider-btn-prev',
        'slider'    => '.js-slider-goods'
    ]

    // слайдер товаров для слайдера с аксессуарами применяем модификатор goods-slider--5items
    ?>

    <div
        class="goods-slider js-slider-2 js-module-require
        <? if ((bool)$categories): ?> goods-slider--width goods-slider--5items<? endif ?>
        <? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?>
        <?= $class ?>"
        data-position="<?= $sender['position'] ?>"

        <? if ($url && !$products) : ?>
            data-module="enter.recommendations"
            data-url="<?= $url . '&' .http_build_query(['senders' => [$sender + ['type' => $type]]]) ?>"
            style="display: none"
        <? endif ?>

        <? if ($products) : ?>
            data-module="jquery.slick"
            data-slick-config='<?= json_encode($slickConfig) ?>'
        <? endif ?>

        data-slider="<?= $helper->json([
            'count'  => $count,
            'limit'  => $limit,
            'url'    => $url,
            'type'   => $type,
            'sender' => $sender,
            'sender2' => $sender2,
        ]) ?>" >

        <? if ($title): ?>
            <h3 class="product-section__h3"><?= $title ?></h3>
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

        <div class="slider-section slider-section_170 goods-slider__inn">
            <ul class="js-slider-goods" style="">

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
                    $category = $product->getParentCategory() ? $product->getParentCategory() : null;
                    ?>

                    <li class="goods-slider-list__i js-module-require"
                        data-module="enter.product"
                        data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>"
                        data-id="<?= $product->getId() ?>"
                        data-product="<?= $helper->json([
                            'id'       => $product->getId(),
                            'ui'       => $product->getUi(),
                            'article'  => $product->getArticle(),
                            'name'     => $product->getName(),
                            'isUpsale' => $product->getIsUpsale(),
                        ]) ?>" >

                        <? if ($product->getLabel()) : ?>
                            <img class="sticker-img" src="<?= $product->getLabel()->getImageUrl() ?>" alt="<?= $product->getLabel()->getName() ?>">
                        <? endif ?>

                        <a id="<?= $elementId ?>" class="goods-slider-list__link" href="<?= $link ?>" target="_blank">

                    <span class="goods-slider-list__action">
                        <img class="goods-slider-list__img" src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>">
                    </span>

                            <span class="goods-slider-list__name"><?= $product->getName() ?></span>
                        </a>

                        <div class="goods-slider-list__price-old">
                            <? if ($product->getPriceOld() && $product->getLabel() && $product->getLabel()->affectPrice) : ?>
                                <span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
                            <? endif ?>
                        </div>

                        <div class="goods-slider-list__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>

                        <?= $helper->render('product/_button.buy', [
                            'product'        => $product,
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

            <div class="goods-slider__btn goods-slider__btn--prev js-goods-slider-btn-prev"></div>
            <div class="goods-slider__btn goods-slider__btn--next js-goods-slider-btn-next"></div>
        </div>
    </div>
    <!--/ слайдер товаров -->


<? }; return $f;