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
    $url = $page->url('cart.product.add', array('productId' => $product->getId()));
}
$btnText = \App::user()->getCart()->hasProduct($product->getId()) ? 'В корзине' : 'Купить';
?>

<a href="<?= $url ?>" data-product="<?= $product->getId() ?>" data-category="<?= $product->getMainCategory() ? $product->getMainCategory()->getId() : 0 ?>" class="link1 bOrangeButton<?php if ($disable): ?> disabled<? endif ?><?php if (!empty($bought)): ?> link1active<? endif ?>"><i></i><span><?= $btnText?></span></a>
