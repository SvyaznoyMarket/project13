<?php
/**
 * @var $page \View\Layout
 * @var $product \Model\Product\Entity|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];
if (!isset($line)) $line = false;

if ($product) {
    $links[] = ['name' => $product->getPrefix(), 'url' => $product->getParentCategory() ? $product->getParentCategory()->getLink() : null, 'last' => false];
    $links[] = ['name' => $product->getWebName(), 'url' => null, 'last' => true];
} ?>

<div class="fixedTopBar__crumbs">
    <div class="fixedTopBar__crumbsImg"><img class="crumbsImg" src="<?= $product ? $product->getImageUrl() : '' ?>" /></div>

    <div class="wrapperCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
        <div class="transGradWhite"></div>
    </div>
</div>

<div class="fixedTopBar__buy <?= $line ? 'hidden' : 'none' ?>">

    <? if ($product->getIsBuyable() && !$product->getKit()): ?>
        <?= $helper->render('cart/__button-product', [
            'product' => $product,
            'class' => 'btnBuy__eLink',
            'value' => 'Купить',
            'onClick' => $addToCartJS ? $addToCartJS : null,
        ]) // Кнопка купить ?>
    <? endif ?>

    <? if ($product->getKit()): ?>
        <?= $helper->render('cart/__button-product-kit', ['product' => $product, 'class' => 'btnBuy__eLink mBuySet', 'value' => 'Купить']) // Кнопка купить ?>
    <? endif ?>

    <? if ($product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId()) && !$product->getKit()): ?>
        <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId())]) ?>
    <? endif ?>

    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

</div>