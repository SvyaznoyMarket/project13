<?php

return function (
    array $products,
    $count = null,
    $limit = null,
    $url = null,
    \Helper\TemplateHelper $helper
) {
/**
 * @var $products \Model\Product\Entity[]
 */

    $sliderData = [
        'count' => $count,
        'limit' => $limit,
        'url'   => $url,
    ];
?>

<div class="bSliderAction<? if (!$limit): ?> mNoSliderAction<? endif ?>" data-slider="<?= $helper->json($sliderData) ?>">

    <div class="bSliderAction__eInner">
        <ul class="bSliderAction__elist clearfix">
        <? foreach ($products as $product): ?>
            <li>
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

    <div class="bSliderAction__eBtn bSliderAction__eDisable bSliderAction__mPrev"><span></span></div>
    <div class="bSliderAction__eBtn bSliderAction__mNext"><span></span></div>
</div>

<? };
