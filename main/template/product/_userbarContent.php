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

<div class="topbarfix_crumbs">
    <div class="topbarfix_crumbsImg"><img class="crumbsImg" src="<?= $product ? $product->getMainImageUrl('product_120') : '' ?>" /></div>

    <div class="wrapperCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>

<div class="topbarfix_buy js-topbarfixBuy none">

    <?= $helper->render('cart/__button-product', [
        'product'  => $product,
        'onClick'  => $addToCartJS ? $addToCartJS : null,
        'sender'   => $buySender,
        'sender2'  => $buySender2,
        'location' => 'userbar',
    ]) // Кнопка купить ?>

    <? if (!$product->getSlotPartnerOffer() && $product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId()) && (!$product->getKit() || $product->getIsKitLocked())): ?>
        <?= $helper->render('__spinner', [
            'id'        => \View\Id::cartButtonForProduct($product->getId()),
            'productId' => $product->getId(),
            'location'  => 'userbar',
        ]) ?>
    <? endif ?>

    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

</div>