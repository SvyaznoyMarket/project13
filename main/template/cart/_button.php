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

<?php
if (empty($view)) {
    $view = 'default';
}

if (empty($quantity)) {
    $quantity = 1;
}

$disabled = !$product->getIsBuyable();

$gaEvent = !empty($gaEvent) ? $gaEvent : null;
$gaTitle = !empty($gaTitle) ? $gaTitle : null;

if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.add', array('productId' => $product->getId())).($page->hasGlobalParam('sender')?(false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender='.$page->getGlobalParam('sender').'|'.$product->getId():'');
}

if (empty($value)) $value = 'Купить';

$productData = [
    'id'           => $product->getId(),
    'mainCategory' => $product->getMainCategory() ? ['id' => $product->getMainCategory()->getId()] : null,
];
?>

<a href="<?= $url ?>"  data-ga-event="<?= $page->escape($gaEvent) ?>" data-ga-title="<?= $page->escape($gaTitle) ?>" data-product="<?= $page->json($productData) ?>" class="js-buy<?php if ($disabled): ?> disabled<? endif ?><?php if ($gaEvent): ?> gaEvent<? endif ?>"><?= $value ?></a>
