<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\Entity
 * @var $disabled bool
 */
?>

<?php
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => 1));
}
?>

<a href="<?= $url ?>" class="link1 bOrangeButton<?php if ($disable): ?> disabled<? endif ?>"><i></i><span>Положить в корзину</span></a>
