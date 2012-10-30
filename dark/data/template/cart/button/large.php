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
    $url = $page->url('old.cart.product.add', array('productId' => $product->getId()));
}
?>

<a href="<?= $url ?>" class="link1 bOrangeButton<?php if ($disable): ?> disabled<? endif ?>"><i></i><span>Положить в корзину</span></a>
