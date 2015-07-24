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

$slickConfig = [
    'slidesToShow' => 4,
    'slidesToScroll' => 4,
    'lazyLoad'  => 'ondemand',
    'dots'      => false,
    'infinite'  => false,
    'nextArrow' => '.js-goods-slider-btn-next',
    'prevArrow' => '.js-goods-slider-btn-prev',
    'slider'    => '.js-slider-goods'
]

?>

<div class="section js-module-require" data-module="jquery.slick" data-slick-config='<?= json_encode($slickConfig) ?>'>
    <div class="section__title"><?= $blockname ?></div>

    <div class="section__content">
        <div class="slider-section">

            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev"></button>

            <div class="goods goods_grid grid-4col js-slider-goods">

            <? foreach ($rrProducts as $product) : ?>

                <? $productLink = $product->getLink() . '?' . http_build_query([
                        'sender[name]'      => 'retailrocket',
                        'sender[position]'  => @$blockname == 'Популярные товары' ? 'MainPopular' : 'MainRecommended',
                        'sender[method]'    => @$blockname == 'Популярные товары' ? 'ItemsToMain' : 'PersonalRecommendation',
                        'sender[from]'      => 'MainPage'
                    ]) ?>

                <div class="goods__item grid-4col__item js-module-require" data-module="enter.product" data-id="<?= $product->getId() ?>">
                    <? if ($product->getLabel()) : ?>
                        <div class="sticker-list">
                            <div class="sticker sticker_sale"><?= $product->getLabel()->getName() ?></div>
                        </div>
                    <? endif ?>

                    <a href="<?= $productLink ?>" class="goods__img">
                        <img data-lazy="<?= $product->getMainImageUrl('product_160') ?>" src="" alt="" class="goods__img-image">
                    </a>

                    <div class="goods__name">
                        <div class="goods__name-inn">
                            <a href="<?= $productLink ?>"><?= $product->getName() ?></a>
                        </div>
                    </div>

                    <div class="goods__price-old"><span class="line-through"><? if ($product->getPriceOld()) : ?><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl-css">P</span><? endif ?></div>

                    <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl-css">P</span></div>

                    <?= $helper->render('product/_button.buy', ['product' => $product, 'data' => []]) ?>
                </div>

            <? endforeach ?>

            </div>
            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next"></button>
        </div>
    </div>
</div>
