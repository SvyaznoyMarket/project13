<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\BasicEntity
 * @var $disabled bool
 * @var $value    string
 * @var $gaEvent  string
 * @var $gaTitle  string
 */
?>

<?
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('old.cart.product.add', array('productId' => $product->getId()));
}

if (empty($value)) $value = '&nbsp;';
?>

<a href="<?= $url ?>"<?php echo (!empty($gaEvent) ? (' data-event="'.$gaEvent.'"') : '').(!empty($gaTitle) ? (' data-title="'.$gaTitle.'"') : '') ?> data-product="<?= $product->getId() ?>" <? include __DIR__ . '/_productData.php' ?> data-category="<?= $product->getMainCategory() ? $product->getMainCategory()->getId() : 0 ?>" class="link1 event-click cart cart-add<?php if ($disabled): ?> disabled<? endif ?><?php if ($gaEvent): ?> gaEvent<? endif ?>"><?= $value ?></a>