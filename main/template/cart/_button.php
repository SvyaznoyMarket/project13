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

if (empty($quantity)) {
    $quantity = 1;
}

if (empty($value)) $value = 'Купить';

$disabled = !$product->getIsBuyable();
if ($disabled) {
    $url = '#';
} else {
    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($page->hasGlobalParam('sender')) {
        $urlParams['sender'] = $page->getGlobalParam('sender') . '|' . $product->getId();
    }
    $url = $page->url('cart.product.add', $urlParams);
}
?>

<a id="<?= sprintf('cartButton-product-%s', $product->getId()) ?>" href="<?= $url ?>" class="jsBuyButton<?php if ($disabled): ?> mDisabled<? endif ?><?php if ($class): ?> <?= $class ?><? endif ?>"><?= $value ?></a>
