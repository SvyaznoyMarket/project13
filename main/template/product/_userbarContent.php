<?php
/**
 * @var $page \View\Layout
 * @var $product \Model\Product\Entity|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];

if ($product) {
    $links[] = ['name' => $product->getParentCategory() ? $product->getParentCategory()->getName() : '', 'url' => $product->getParentCategory() ? $product->getParentCategory()->getLink() : null, 'last' => false];
    $links[] = ['name' => $product->getName(), 'url' => null, 'last' => true];
}

$buySender = $request->get('sender');
$buySender2 = $request->get('sender2');
?>

<div class="userbar-crumbs">
    <div class="userbar-crumbs-img"><img class="userbar-crumbs-img__img" src="<?= $product ? $product->getImageUrl() : '' ?>" /></div>

    <div class="userbar-crumbs-wrap">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>

<? if (!$product->getIsBuyable()) return ?>

<div class="topbarfix_buy js-topbarfixBuy none">

    <div class="topbarfix_buy-price">
        <? if ($product->getPriceOld()) : ?>
        <div class="product-card-old-price" style="font-size: 12px;">
            <span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
        </div>
        <? endif ?>

        <span class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></span><span class="rubl">p</span>
    </div>

    <?= $helper->render('cart/__button-product', [
        'product'  => $product,
        'onClick'  => $addToCartJS ? $addToCartJS : null,
        'sender'   => $buySender,
        'sender2'  => $buySender2,
        'noUpdate'  => true,
        'location' => 'userbar',
    ]) // Кнопка купить ?>


</div>