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

    <ul class="bSliderAction__elist clearfix">
    <? foreach ($products as $product): ?>
        <li>
            <div class="product__inner">
                <a class="productImg" href=""><img src="<?= $product->getImageUrl() ?>" alt="<?= $helper->escape($product->getName()) ?>" /></a>
                <div class="reviewSection__star clearfix reviewSection100__star">
                    <img src="/images/reviews_star.png">
                    <img src="/images/reviews_star.png">
                    <img src="/images/reviews_star.png">
                    <img src="/images/reviews_star.png">
                    <img src="/images/reviews_star_empty.png">
                </div>
                <div class="productName"><a href="<?= $helper->url('product', ['productPath' => $product->getPath()]) ?>"><?= $product->getName() ?></a></div>
                <div class="productPrice"><span class="price"><?= $helper->formatPrice($product->getPrice()) ?>p</span></div>
                <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
            </div>
        </li>
    <? endforeach ?>
    </ul>

    <div class="bSliderAction__eBtn bSliderAction__eDisable bSliderAction__mPrev"><span></span></div>
    <div class="bSliderAction__eBtn bSliderAction__mNext"><span></span></div>
</div>

<? };
