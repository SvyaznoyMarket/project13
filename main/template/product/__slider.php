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

?>
<div
    id="<?= $id ?>"
    data-position="<?= $sender['position'] ?>"
    class="bGoodsSlider js-slider clearfix<? if ((bool)$categories): ?> mWithCategory<? endif ?><? if ($url && !(bool)$products): ?> <? endif ?><? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?>"
    data-slider="<?= $helper->json([
        'count'  => $count,
        'limit'  => $limit,
        'url'    => $url,
        'type'   => $type,
        'sender' => $sender,
        'sender2' => $sender2,
    ]) ?>"
    <? if ($containerStyle): ?> style="<?= $containerStyle ?>" <? endif ?>
>
    <? if ($title): ?>
        <h3 class="bHeadSection"><?= $title ?></h3>
    <? endif ?>

    <? if ((bool)$categories): ?>
        <div class="bGoodsSlider__eCat">
            <ul>
                <? $i = 0; foreach ($categories as $category): ?>
                    <li id="<?= $sliderId . '-category-' . $category->getId() ?>"
                        class="bGoodsSlider__eCatItem <? if (0 == $i): ?> mActive<? endif ?>"
                        data-product="<?= $category->getId() ? 'self' : 'all' ?>">
                        <span><?= $category->getName() ?></span>
                    </li>
                <? $i++; endforeach ?>
            </ul>
        </div>
    <? endif ?>

    <div class="slideItem<? if ($class): ?> <?= $class ?><? endif ?>">
        <div class="slideItem_cntr"><? if ($showPageCounter): ?>Страница 1 из 8<? endif ?></div>

        <? if ($isCompact): ?>
            <div class="slideItem_flt">
                <div class="slideItem_flt_i"></div>
            </div>
        <? endif ?>

        <div class="slideItem_inn mLoader">
            <ul class="slideItem_lst clearfix">
            <? foreach ($products as $index => $product):

                /** @var $product \Model\Product\Entity */

                // разбиение слайдера на несколько строк
                if ($rowsCount == 1) {
                    $needStartLiTag = true;
                    $needCloseLiTag = true;
                } else {
                    $i1 = $index % $rowsCount;
                    $needStartLiTag = 0 == $i1;
                    $needCloseLiTag = 0 == $rowsCount - $i1 - 1;
                    if (count($products) == $index + 1) $needCloseLiTag = true;
                }

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
                <? if ($needStartLiTag) : ?>
                <li
                    class="slideItem_i jsRecommendedItem jsSliderItem <? if ($product->getSlotPartnerOffer()): ?>slot--centered<? endif ?>"
                    data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>"
                    data-product="<?= $helper->json([
                        'article'  => $product->getArticle(),
                        'name'     => $product->getName(),
                        'isUpsale' => $product->getIsUpsale(),
                    ]) ?>"
                >
                <? endif ?>
                <div class="slideItem_i__child">
                    <? if ('top' == $namePosition): ?>
                        <div class="slideItem_n">
                            <a id="<?= $elementId ?>" <? if ($isRetailrocketProduct): ?>class="jsRecommendedItem" <? endif ?> href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?> <?= $linkTarget ?>><?= $product->getName() ?></a>
                        </div>
                    <? endif ?>

                    <? if ((bool)$product->getLabel()): ?>
                        <img class="slideItem_stick" src="<?= $product->getLabel()->getImageUrl(0) ?>" alt="<?= $product->getLabel()->getName() ?>" />
                    <? endif ?>

                    <a id="<?= $elementId . '-image' ?>" class="<? if ($isRetailrocketProduct): ?>jsRecommendedItem <? endif ?>slideItem_imgw<? if($product->getIsUpsale()): ?> jsUpsaleProduct<? endif; ?>" href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?> <?= $linkTarget ?>>
                        <img class="slideItem_img" src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                    </a>

                    <? if (!$isCompact): ?>

                        <? if ('bottom' == $namePosition) : ?>
                            <div class="slideItem_n">
                                <a id="<?= $elementId ?>" <? if ($isRetailrocketProduct): ?>class="jsRecommendedItem" <? endif ?> href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?> <?= $linkTarget ?>><?= $product->getName() ?></a>
                            </div>
                        <? endif ?>

                        <div class="slideItem_pr"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

                        <?= $helper->render('cart/__button-product', [
                            'product'        => $product,
                            'onClick'        => $addToCartJS ? $addToCartJS : null,
                            'isRetailRocket' => $isRetailrocketProduct, // TODO: удалить
                            'sender'         => $sender,
                            'noUpdate'       => true,
                            'location'       => 'slider',
                            'sender2'       => $sender2,
                        ]) // Кнопка купить ?>
                    <? endif ?>
                </div>
            <? if ($needCloseLiTag) : ?></li><? endif ?>
            <? endforeach ?>
            </ul>
        </div>

        <div class="slideItem_btn slideItem_btn-prv mDisabled<? if ('retailrocket' == $sender['name']): ?> jsRecommendedSliderNav<? endif ?>"></div>
        <div class="slideItem_btn slideItem_btn-nxt mDisabled<? if ('retailrocket' == $sender['name']): ?> jsRecommendedSliderNav<? endif ?>"></div>
    </div>

</div><?/*<!--/product accessory section -->*/?>

<? }; return $f;
