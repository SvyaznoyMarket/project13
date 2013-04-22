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

<a href="<?= $url ?>" data-product="<?= $product->getId() ?>" <? include __DIR__ . '/_productData.php' ?> data-category="<?= $product->getMainCategory() ? $product->getMainCategory()->getId() : 0 ?>" class="link1 bOrangeButton<?php if ($disable): ?> disabled<? endif ?>"><i></i><span>Положить в корзину</span></a>
