<?php

return function (
    \Helper\TemplateHelper $helper,
    array $products,
    $title = null,
    array $categories = [],
    $class = null,
    $count = null,
    $limit = null,
    $url = null,
    $type = null,
    $isRetailrocketRecommendation = false,
    $retailrocketMethod = null,
    $retailrocketIds = []
) {
/**
 * @var $products   \Model\Product\Entity[]
 * @var $categories \Model\Product\Category\Entity[]
 */

    $sliderId = 'slider-' . uniqid();
?>
<div class="bGoodsSlider js-slider clearfix<? if ((bool)$categories): ?> mWithCategory<? endif ?><? if ($url && !(bool)$products): ?> <? endif ?><? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?>"  data-slider="<?= $helper->json([
        'count' => $count,
        'limit' => $limit,
        'url'   => $url,
        'type'  => $type,
    ]) ?>">
    <? if ($title): ?>
        <h3 class="bHeadSection"><?= $title ?></h3>
    <? endif ?>

    <? if ((bool)$categories): ?>
        <div class="bGoodsSlider__eCat">
            <ul>
                <? $i = 0; foreach ($categories as $category): ?>
                    <li id="<?= $sliderId . '-category-' . $category->getId() ?>" class="bGoodsSlider__eCatItem <? if (0 == $i): ?> mActive<? endif ?>" data-product="<?= $category->getId() ? 'self' : 'all' ?>">
                        <span><?= $category->getName() ?></span>
                    </li>
                <? $i++; endforeach ?>
            </ul>
        </div>
    <? endif ?>

    <div class="bSlider<? if ($class): ?> <?= $class ?><? endif ?>">

        <div class="bSlider__eInner mLoader">
            <ul class="bSlider__eList clearfix">
            <? foreach ($products as $product):
                if (!$product instanceof \Model\Product\Entity) continue;

                $link = $helper->url('product', ['productPath' => $product->getPath()]);

                // Retailrocket
                $isRetailrocketProduct = is_array($retailrocketIds) && in_array($product->getId(), $retailrocketIds);
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
                <li class="bSlider__eItem jsSliderItem" data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>" data-product="<?= $helper->json([
                        'article' => $product->getArticle(),
                        'name' => $product->getName(),
                        'isUpsale' => $product->getIsUpsale(),
                    ]) ?>">
                    <div class="product__inner">
                        <? if ((bool)$product->getLabel()): ?>
                            <img class="bProductDescSticker" src="<?= $product->getLabel()->getImageUrl(0) ?>" alt="<?= $product->getLabel()->getName() ?>" />
                        <? endif ?>
                        <a class="productImg<? if($product->getIsUpsale()): ?> jsUpsaleProduct<? endif; ?>" href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?>>
                            <img src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                        </a>
                        <div class="productName"><a href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?>><?= $product->getName() ?></a></div>
                        <div class="productPrice"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

                        <? if ($product->getKit() && !$product->getIsKitLocked()) : ?>
                            <a class="btnView mBtnGrey" href="<?= $product->getLink() ?>">Посмотреть</a> <!--TODO-zra стиль для кнопки "Посмотреть" -->
                        <? else: ?>
                            <?= $helper->render('cart/__button-product', [
                                'product'    => $product,
                                'onClick'    => $addToCartJS ? $addToCartJS : null,
                            ]) // Кнопка купить ?>
                        <? endif ?>
                    </div>
                </li>
            <? endforeach ?>
            </ul>
        </div>

        <div class="bSlider__eBtn mPrev mDisabled"><span></span></div>
        <div class="bSlider__eBtn mNext mDisabled"><span></span></div>
    </div>

</div><?/*<!--/product accessory section -->*/?>

<? };
