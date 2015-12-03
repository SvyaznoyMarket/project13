<?php
/**
 * @var $page \View\Main\IndexPage
 * @var $products \Model\Product\Entity[]
 */

if (\App::abTest()->getViewedOnMainCase() !== 2) {
    return;
}

$helper = \App::helper();
$sender = [
    'name'      => 'enter',
    'position'  => 'Interest_Main',
];

if (!isset($products)) :
?>

<div class="js-viewed-slider" data-slider="<?= $helper->json([
    'url' => $helper->url('product.viewed', ['template' => 'main/_viewed.modern'])
]) ?>"></div>

<? elseif ($products) :

foreach ($products as $key => $product) {
    if (!$product->isAvailable() && !$product->isInShopShowroomOnly()) {
        unset($products[$key]);
    }
}

endif; if (count($products) >= 4 ) : ?>

<div class="slidesBox slidesBox-intrested slidesBox-full jsViewedBlock">
    <div class="slidesBox_h">
        <? if (count($products) > 4) : ?>
        <div class="slidesBox_btn slidesBox_btn-l jsViewedBlockArror" data-direction="-1"></div>
        <? endif ?>

        <div class="slidesBox_h_c">
            <div class="slidesBox_t">ВАМ БЫЛО ИНТЕРЕСНО</div>

            <ul class="slidesBox_dott">
                <? foreach (array_chunk($products, 4) as $i => $chunk) : ?>
                <li class="slidesBox_dott_i jsViewedBlockDot <?= $i !== 0 ? : 'slidesBox_dott_i-act' ?>"></li>
                <? endforeach ?>
            </ul>

        </div>
        <? if (count($products) > 4) : ?>
        <div class="slidesBox_btn slidesBox_btn-r jsViewedBlockArror" data-direction="1"></div>
        <? endif ?>
    </div>

    <div class="slidesBox_inn">
        <div style="overflow:hidden;"><ul class="slider-goods-boxes slidesBox_lst clearfix jsViewedBlockHolder">

                <li class="slider-goods-boxes__item slidesBox_i">

                    <? foreach ($products as $product) : ?>

                        <div class="slider-goods-boxes__box">

                            <div class="slider-goods-boxes__rating">
                                <? if ($product->getAvgStarScore()) : ?>
                                    <div class="rating rating_mini">
                            <span class="rating__state">
                                <? foreach (range(1,5) as $r) : ?>
                                    <i class="rating__item <?= $r <= $product->getAvgStarScore() ? 'rating__item_fill' : '' ?>"></i>
                                <? endforeach ?>
                            </span>
                                    </div>
                                <? endif ?>
                            </div>

                            <a href="<?= $product->getLink(['sender' => $sender + ['from' => 'Main']]) ?>" class="slider-goods-boxes__link jsProductLinkViewedMain">
                        <span class="slider-goods-boxes__link-image">
                            <img src="<?= $product->getImageUrl() ?>" alt="<?= $product->getName() ?>" class="image">
                        </span>
                                <span class="slider-goods-boxes__link-name"><?= $product->getName() ?></span>
                            </a>

                            <div class="slider-goods-boxes__price">
                                <span class="slider-goods-boxes__price-current"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span>
                                <? if ($product->getPriceOld()) : ?>
                                    <span class="slider-goods-boxes__price-old"><span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></span>
                                <? endif ?>
                            </div>

                            <?= $helper->render('cart/__button-product', [
                                'product'        => $product,
                                'sender'         => $sender,
                                'noUpdate'       => true,
                                'location'       => 'slider',
                                'useNewStyles'   => false,
                                'class'          => ''
                            ]) // Кнопка купить ?>

                        </div>

                    <? endforeach ?>

                </li>
            </ul></div>

    </div>
</div>

<? endif ?>