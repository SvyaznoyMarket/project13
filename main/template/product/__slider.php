<?php

return function (
    \Helper\TemplateHelper $helper,
    array $products,
    $title = null,
    array $categories = [],
    $class = null,
    $count = null,
    $limit = null,
    $url = null
) {
/**
 * @var $products   \Model\Product\Entity[]
 * @var $categories \Model\Product\Category\Entity[]
 */

    $sliderId = 'slider-' . uniqid();
?>
<div class="bGoodsSlider clearfix<? if ((bool)$categories): ?> mWithCategory<? endif ?><? if ($url && !(bool)$products): ?> hidden<? endif ?>">
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

    <div class="bSliderAction<? if ($class): ?> <?= $class ?><? endif ?>" data-slider="<?= $helper->json([
        'count' => $count,
        'limit' => $limit,
        'url'   => $url,
    ]) ?>">

        <div class="bSliderAction__eInner">
            <ul class="bSliderAction__eList clearfix">
            <? foreach ($products as $product): ?>
            <?
                $category = $product->getParentCategory() ? $product->getParentCategory() : null;
            ?>
                <li class="bSliderAction__eItem" data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>">
                    <div class="product__inner">
                        <a class="productImg" href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><img src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" /></a>
                        <div class="productName"><a href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><?= $product->getName() ?></a></div>
                        <div class="productPrice"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

                        <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'Купить']) // Кнопка купить ?>
                    </div>
                </li>
            <? endforeach ?>
            </ul>
        </div>

        <div class="bSliderAction__eBtn mPrev mDisabled"><span></span></div>
        <div class="bSliderAction__eBtn mNext mDisabled"><span></span></div>
    </div>

</div><!--/product accessory section -->

<? };
