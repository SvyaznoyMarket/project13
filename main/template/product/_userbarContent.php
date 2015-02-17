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
?>

<div class="topbarfix_crumbs">
    <div class="topbarfix_crumbsImg"><img class="crumbsImg" src="<?= $product ? $product->getImageUrl() : '' ?>" /></div>

    <div class="wrapperCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>

<div class="topbarfix_buy js-topbarfixBuy <?= $line ? 'hidden' : 'none' ?>">

    <? if (!$product->getKit() || $product->getIsKitLocked()): ?>
        <?= $helper->render('cart/__button-product', [
            'product'  => $product,
            'onClick'  => $addToCartJS ? $addToCartJS : null,
            'sender'   => ($request->get('sender') ? (array)$request->get('sender') : $productPageSender) + ['name' => null, 'method' => null, 'position' => null],
            'location' => 'userbar',
        ]) // Кнопка купить ?>
    <? else: ?>
        <?= $helper->render('cart/__button-product-kit', [
            'product'  => $product,
            'sender'   => ($request->get('sender') ? (array)$request->get('sender') : $productPageSender) + ['name' => null, 'method' => null, 'position' => null],
        ]) // Кнопка купить ?>
    <? endif ?>

    <? if (!$product->getSlotPartnerOffer() && $product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId()) && (!$product->getKit()) || $product->getIsKitLocked()): ?>
        <?= $helper->render('__spinner', [
            'id'        => \View\Id::cartButtonForProduct($product->getId()),
            'productId' => $product->getId(),
            'location'  => 'userbar',
        ]) ?>
    <? endif ?>

    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

</div>