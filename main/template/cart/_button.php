<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $product  \Model\Product\Entity
 */
?>

<?
/** @var $region \Model\Region\Entity|null */
$region = \App::user()->getRegion();
$forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

$class = ' ' . \View\Id::cartButtonForProduct($product->getId());

if (!$product->isInShopStockOnly() && $forceDefaultBuy) {
    $class .= ' jsBuyButton btnBuy__eLink';
}

$value = 'Купить';

if ($product->isInShopStockOnly() && $forceDefaultBuy) {
    $value = 'Резерв';
    $url = $page->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
}

if (!$product->getIsBuyable()) {
    $url = '#';
    $class .= ' mDisabled';
    $value = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет в наличии';
} else if (!isset($url)) {
    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($page->hasGlobalParam('sender')) {
        $urlParams['sender'] = $page->getGlobalParam('sender') . '|' . $product->getId();
    }
    $url = $page->url('cart.product.set', $urlParams);
}

$upsaleData = [
    'url' => $page->url('product.upsale', ['productId' => $product->getId()]),
    'fromUpsale' => ($page->hasGlobalParam('from') && 'cart_rec' === $page->getGlobalParam('from')) ? true : false,
];
?>

<div class="bWidgetBuy__eBuy btnBuy">
    <a href="<?= $url ?>" class="<?= $class ?>" data-product-id="<?= $product->getId() ?>" data-upsale="<?= $page->json($upsaleData) ?>" data-bind="buyButtonBinding: cart"><?= $value ?></a>
</div>
