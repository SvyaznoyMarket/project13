<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @param array|null $sender
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $sender = null
) {
    $link = $helper->url('product', [
        'productPath' => $product->getPath(),
        'sender'      => [
            'name'     => @$sender['name'],
            'method'   => @$sender['method'],
            'position' => @$sender['position'],
        ],
    ]);

    $id = 'productLink-' . $product->getId() . '-' . md5(json_encode($sender));

    // открытие товаров в новом окне
    $linkTarget = \App::abTest()->isNewWindow() ? ' target="_blank" ' : '';
?>

<div class="navitem">
    <div class="navitem_tl">ТОВАР ДНЯ</div>
    <a id="<?= $id ?>" href="<?= $link ?>" class="navitem_cnt jsRecommendedItemInMenu" data-sender="<?= $helper->json($sender)?>" <?= $linkTarget ?>>
        <div class="navitem_img-wrap">
            <img data-src="<?= $product->getMainImageUrl('product_500') ?>" alt="<?= $helper->escape($product->getName()) ?>" class="navitem_img menuImgLazy">
        </div>
        <noscript><img src="<?= $product->getMainImageUrl('product_500') ?>" alt="<?= $helper->escape($product->getName()) ?>" class="navitem_img"></noscript>
        <span class="navitem_n"><?= $product->getName() ?></span>
    </a>

    <div class="navitem_pr">
        <?= $helper->formatPrice($product->getPrice())?> <span class="rubl">p</span>
    </div>
</div>

<? }; return $f;