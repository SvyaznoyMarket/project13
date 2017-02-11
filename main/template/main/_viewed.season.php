<?php
/**
 * @var $page \View\Main\IndexPage
 * @var $products \Model\Product\Entity[]
 */

if (\App::abTest()->getViewedOnMainCase() !== 3) {
    return;
}

$helper = \App::helper();
$sender = [
    'name'      => 'enter',
    'position'  => 'Present_Main',
];

if (!isset($products)) :
?>

<div class="js-viewed-slider" data-slider="<?= $helper->json([
    'url' => $helper->url('product.viewed', ['template' => 'main/_viewed.season'])
]) ?>"></div>

<? elseif ($products) :

    foreach ($products as $key => $product) {
        if (!$product->isAvailable() && !$product->isInShopShowroomOnly()) {
            unset($products[$key]);
        }
    }

endif; if(count($products) >= 4) : ?>

<div class="newyear-gifts jsSeasonViewed">
    <div class="newyear-gifts__title">ВЫБЕРИ ПОДАРКИ СЕБЕ И БЛИЗКИМ</div>

    <i class="i-newyear-gifts-light i-newyear-gifts-light_blue"></i>
    <i class="i-newyear-gifts-light i-newyear-gifts-light_green"></i>
    <i class="i-newyear-gifts-light i-newyear-gifts-light_orange"></i>
    <i class="i-newyear-gifts-light i-newyear-gifts-light_red"></i>
    
    <div class="newyear-gifts-slider-wrap">
        <ul class="newyear-gifts-slider jsSeasonViewedHolder" data-count="<?= count($products) ?>">

        <? foreach ($products as $product) : ?>

            <li class="newyear-gifts-slider__item">
                <a href="<?= $product->getLink() . '#' . http_build_query(['sender' => $sender + ['from' => 'Main']]) ?>" class="newyear-gifts-slider__image jsProductLinkViewedMain">
                    <img src="<?= $product->getImageUrl() ?>" alt="<?= $product->getName() ?>" class="image">
                </a>

                <a href="<?= $product->getLink() . '#' . http_build_query(['sender' => $sender + ['from' => 'Main']]) ?>" class="newyear-gifts-slider__name jsProductLinkViewedMain"><?= $product->getName() ?></a>

                <div class="newyear-gifts-slider__price">
                    <span class="newyear-gifts-slider__price-current">
                        <? if ($product->isGifteryCertificate()): ?>
                            <?= 'от ' . \App::config()->partners['Giftery']['lowestPrice'] ?>
                        <? else: ?>
                            <?= $helper->formatPrice($product->getPrice()) ?>
                        <? endif ?>
                        
                        <span class="rubl">p</span>
                    </span>
                    <? if ($product->getPriceOld()) : ?>
                    <span class="newyear-gifts-slider__price-old"><span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></span>
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

            </li>

        <? endforeach ?>

        </ul>
    </div>

    <? if (count($products) > 4) : ?>
        <div class="newyear-gifts__btn newyear-gifts__btn_prev disabled jsSeasonBtn" data-direction="-1"></div>
        <div class="newyear-gifts__btn newyear-gifts__btn_next jsSeasonBtn" data-direction="1"></div>
    <? endif ?>
</div>
<? endif ?>