<?php

return function (
    array $products,
    array $categories = [],
    $count = null,
    $limit = null,
    $url = null,
    \Helper\TemplateHelper $helper
) {
/**
 * @var $products   \Model\Product\Entity[]
 * @var $categories \Model\Product\Category\Entity[]
 */

    /** @var $firstCategory \Model\Product\Category\Entity|null */
    $firstCategory = (bool)$categories ? reset($categories) : null;

    $sliderId = 'slider-' . uniqid();
?>
<div class="bGoodsSlider clearfix <? if ((bool)$categories): ?>mWithCategory<? endif ?>">

    <? if ((bool)$categories): ?>
        <div class="bGoodsSlider__eCat">
            <ul>
                <? $i = 0; foreach ($categories as $category): ?>
                    <li id="<?= $sliderId . '-category-' .$category->getId() ?>"<? if (0 == $i): ?> class="mActive"<? endif ?>>
                        <span><?= $category->getName() ?></span>
                    </li>
                <? $i++; endforeach ?>
            </ul>
        </div>
    <? endif ?>

    <div class="bSliderAction<? if (!$limit): ?> mNoSliderAction<? endif ?>" data-slider="<?= $helper->json([
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
                <li class="bSliderAction__eItem<? if (!($firstCategory && $category && ($firstCategory->getId() == $category->getId()))): ?> hidden<? endif ?>" data-category="<?= $category ? ($sliderId . '-category-' . $category->getId()) : null ?>">
                    <div class="product__inner">
                        <a class="productImg" href=""><img src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" /></a>
                        <div class="productName"><a href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><?= $product->getName() ?></a></div>
                        <div class="productPrice"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>
                        <div class="btnBuy">
                            <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'В корзину']) ?>
                        </div>
                    </div>
                </li>
            <? endforeach ?>
            </ul>
        </div>

        <div class="bSliderAction__eBtn mPrev mDisable"><span></span></div>
        <div class="bSliderAction__eBtn mNext mDisable"><span></span></div>
    </div>

</div><!--/product accessory section -->

<? };
