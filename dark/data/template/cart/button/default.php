<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\Entity
 * @var $disabled bool
 * @var $value    string
 */
?>

<?php
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => 1));
}

if (empty($value)) $value = '&nbsp;'
?>

<a href="<?= $url ?>" class="link1 event-click cart cart-add<?php if ($disabled): ?> disable<? endif ?>"><?= $value ?></a>