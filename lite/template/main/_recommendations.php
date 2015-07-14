<?php

use \Model\Product\Entity as Product;

/**
 * @var $page           \View\Main\IndexPage
 * @var $blockname      string
 * @var $class          string|null
 * @var $productList    Product[]
 * @var $rrProducts     array
 * @var $sender         array
 */

// фильтруем массив rr
foreach ($rrProducts as &$value) {
    if (@$productList[$value] instanceof Product) {
        $value = $productList[$value];
    } else {
        unset($value);
    }
} if (isset($value)) unset($value);

/** @var $rrProducts Product[] */

$helper = new \Helper\TemplateHelper();
$sliderToken = $blockname == 'Популярные товары' ? 'pop' : 'hit';
?>

<div class="section js-module-require" data-module="jquery.slick">
    <div class="section__title"><?= $blockname ?></div>

    <div class="section__content">
        <div class="slider-section">

            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-<?= $sliderToken ?>"></button>

            <div class="goods goods_grid grid-4col js-slider-goods js-slider-goods-<?= $sliderToken ?>" data-slick-slider="<?= $sliderToken ?>" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>

            <? foreach ($rrProducts as $product) : ?>

                <? $productLink = $product->getLink() . '?' . http_build_query([
                        'sender[name]'      => 'retailrocket',
                        'sender[position]'  => @$blockname == 'Популярные товары' ? 'MainPopular' : 'MainRecommended',
                        'sender[method]'    => @$blockname == 'Популярные товары' ? 'ItemsToMain' : 'PersonalRecommendation',
                        'sender[from]'      => 'MainPage'
                    ]) ?>

                <div class="goods__item grid-4col__item">
                    <? if ($product->getLabel()) : ?>
                        <div class="sticker"><?= $product->getLabel()->getName() ?></div>
                    <? endif ?>

                    <a href="<?= $productLink ?>" class="goods__img">
                        <img data-lazy="<?= $product->getMainImageUrl('product_160') ?>" src="" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <a href="<?= $productLink ?>"><?= $product->getName() ?></a>
                    </div>

                    <div class="goods__price-old"><span class="line-through"><? if ($product->getPriceOld()) : ?><?= $helper->formatPrice($product->getPriceOld()) ?></span> &#8399;<? endif ?></div>

                    <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice()) ?> &#8399;</div>

                    <a class="goods__btn btn-primary" href="">Купить</a>
                </div>

            <? endforeach ?>

            </div>
            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-<?= $sliderToken ?>"></button>
        </div>
    </div>
</div>
