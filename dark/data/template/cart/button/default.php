<?php
/**
 * @var $page \View\DefaultLayout
 * @var $user \Session\User
 * @var $product \Model\Product\Entity
 * @var $disabled bool
 * @var $value string
 */
?>

<?php
if ($disabled) {
    $url = '#';
} else if ($user->getCart()->hasProduct($product->getId())) {
    $url = $page->url('cart');
} else {
    $url = $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => 1));
}

if (empty($value)) $value = '&nbsp;'
?>

<a href="<?= $url ?>" class="link1 event-click cart cart-add<?php if ($disabled): ?> disable<? endif ?>"><?= $value ?></a>