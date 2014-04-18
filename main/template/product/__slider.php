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
    $type = null
) {
/**
 * @var $products   \Model\Product\Entity[]
 * @var $categories \Model\Product\Category\Entity[]
 */

    $sliderId = 'slider-' . uniqid();
?>
<div class="bGoodsSlider clearfix<? if ((bool)$categories): ?> mWithCategory<? endif ?><? if ($url && !(bool)$products): ?> <? endif ?><? if (!(bool)$url && !(bool)$products): ?> hf<? endif ?>"  data-slider="<?= $helper->json([
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
            <? foreach ($products as $product): ?>
            <?
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
                        <a class="productImg<? if($product->getIsUpsale()): ?> jsUpsaleProduct<? endif; ?>" href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><img src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" /></a>
                        <div class="productName"><a href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><?= $product->getName() ?></a></div>
                        <div class="productPrice"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

                        <? if (!$product->getKit()) : ?>
                            <?= $helper->render('cart/__button-product', [
                                'product'    => $product,
                                'class'      => 'btnBuy__eLink',
                                'value'      => 'Купить',
                                'directLink' => true,
                            ]) // Кнопка купить ?>
                        <? endif ?>
                        <? if ($product->getKit()) : ?>
                        <a class="btnView mBtnGrey" href="<?= $product->getLink() ?>">Посмотреть</a> <!--TODO-zra стиль для кнопки "Посмотреть" -->
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
