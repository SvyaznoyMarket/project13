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
    $retailrocketIds = [],
    $namePosition = 'bottom',
    array $sender = []
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

    <div class="slideItem<? if ($class): ?> <?= $class ?><? endif ?>">
        <div class="slideItem_cntr"><!--Страница 2 из 8--></div>

        <div class="slideItem_inn mLoader">
            <ul class="slideItem_lst clearfix">
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
                <li
                    class="slideItem_i jsSliderItem"
                    data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>"
                    data-product="<?= $helper->json([
                        'article' => $product->getArticle(),
                        'name' => $product->getName(),
                        'isUpsale' => $product->getIsUpsale(),
                    ]) ?>"
                >
                    <? if ('top' == $namePosition): ?>
                        <div class="slideItem_n"><a href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?>><?= $product->getName() ?></a></div>
                    <? endif ?>

                    <? if ((bool)$product->getLabel()): ?>
                        <img class="slideItem_stick" src="<?= $product->getLabel()->getImageUrl(0) ?>" alt="<?= $product->getLabel()->getName() ?>" />
                    <? endif ?>

                    <a class="slideItem_imgw<? if($product->getIsUpsale()): ?> jsUpsaleProduct<? endif; ?>" href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?>>
                        <img class="slideItem_img" src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                    </a>

                    <? if ('bottom' == $namePosition): ?>
                        <div class="slideItem_n"><a href="<?= $link ?>"<? if ($isRetailrocketRecommendation && $linkClickJS): ?> onmousedown="<?= $linkClickJS ?>"<? endif ?>><?= $product->getName() ?></a></div>
                    <? endif ?>

                    <div class="slideItem_pr"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

                    <? if ($product->getKit() && !$product->getIsKitLocked()) : ?>
                        <a class="btnView mBtnGrey" href="<?= $product->getLink() ?>">Посмотреть</a> <!--TODO-zra стиль для кнопки "Посмотреть" -->
                    <? else: ?>
                        <?= $helper->render('cart/__button-product', [
                            'product'        => $product,
                            'onClick'        => $addToCartJS ? $addToCartJS : null,
                            'isRetailRocket' => $isRetailrocketProduct,
                            'sender'         => $sender,
                        ]) // Кнопка купить ?>
                    <? endif ?>
                </li>
            <? endforeach ?>
            </ul>
        </div>

        <div class="slideItem_btn slideItem_btn-prv mDisabled"></div>
        <div class="slideItem_btn slideItem_btn-nxt mDisabled"></div>
    </div>

</div><?/*<!--/product accessory section -->*/?>

<? };
