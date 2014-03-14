<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $product  \Model\Product\Entity
 * @var $view     string
 * @var $quantity int
 * @var $gaEvent  string
 * @var $gaTitle  string
 */
?>

<?
/** @var $region \Model\Region\Entity|null */
$region = \App::user()->getRegion();
$forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

if (!isset($class)) {
    $class = '';
}
$class .= ' ' . \View\Id::cartButtonForProduct($product->getId());

if (!$product->isInShopStockOnly() && $forceDefaultBuy) {
    $class .= ' jsBuyButton btnBuy__eLink';
}

if (empty($quantity)) {
    $quantity = 1;
}

if (empty($value)) $value = 'Купить';

$disabled = !$product->getIsBuyable();

if ($product->isInShopStockOnly() && $forceDefaultBuy) {
    $value = 'Резерв';
    $url = $page->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
}

if ($disabled) {
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
?>

<div class="bWidgetBuy__eBuy btnBuy">
    <a href="<?= $url ?>" class="<?= $class ?>" data-group="<?= $product->getId() ?>"><?= $value ?></a>
</div>
