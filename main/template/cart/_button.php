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
if (!isset($class)) {
    $class = '';
}
$class .= ' ' . \View\Id::cartButtonForProduct($product->getId()) . ' jsBuyButton';

if (empty($quantity)) {
    $quantity = 1;
}

if (empty($value)) $value = 'Купить';

$disabled = false;

if (!$product->getIsBuyable()) {
    $disabled = true;
    $value = 'Нет в наличии';
}

if ($product->getIsInShopsOnly()) {
    $value = 'Только в магазинах';
} elseif ($product->getState()->getIsShop()) {
    $value = 'Витринный товар';
}


if ($disabled) {
    $url = '#';
    $class .= ' mDisabled';
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
