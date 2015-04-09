<?php
/**
 * @var $page \View\Layout
 * @var $product \Model\Product\Entity|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];
if (!isset($line)) $line = false;

if ($product) {
    $links[] = ['name' => $product->getParentCategory() ? $product->getParentCategory()->getName() : '', 'url' => $product->getParentCategory() ? $product->getParentCategory()->getLink() : null, 'last' => false];
    $links[] = ['name' => $product->getName(), 'url' => null, 'last' => true];
}

$productPageSender = \Session\ProductPageSenders::get($product->getUi());
$productPageSender2 = \Session\ProductPageSendersForMarketplace::get($product->getUi());
?>

<div class="userbar-crumbs">
    <div class="userbar-crumbs-img"><img class="userbar-crumbs-img__img" src="<?= $product ? $product->getImageUrl() : '' ?>" /></div>

    <div class="userbar-crumbs-wrap">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>

<div class="topbarfix_buy js-topbarfixBuy <?= $line ? 'hidden' : 'none' ?>">

    <?/*= $helper->render('cart/__button-product', [
        'product'  => $product,
        'onClick'  => $addToCartJS ? $addToCartJS : null,
        'sender'   => ($request->get('sender') ? (array)$request->get('sender') : $productPageSender) + ['name' => null, 'method' => null, 'position' => null],
        'location' => 'userbar',
        'sender2'  => $productPageSender2,
    ]) // Кнопка купить */?>

    <div class="topbarfix_buy-price">
        <? if ($product->getPriceOld()) : ?>
        <div class="product-card-old-price" style="font-size: 12px;">
            <span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
        </div>
        <? endif ?>

        <span class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></span><span class="rubl">p</span>
    </div>

    <a href="<?= \App::helper()->url('cart') ?>" class="topbarfix_buy-btn btn-type btn-type--buy">В корзину</a>
</div>