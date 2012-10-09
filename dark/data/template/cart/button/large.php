<?php
/**
 * @var $page \View\DefaultLayout
 * @var $user \Session\User
 * @var $product \Model\Product\Entity
 * @var $disabled bool
 */
?>

<?php
if ($disabled) {
    $url = '#';
} else if ($user->getCart()->hasProduct($product->getId())) {
    $url = $page->url('cart');
} else {
    $url = $page->url('order.new');
}
?>

<? if ($user->getCart()->hasProduct($product)): ?>
    <a href="<?= $url ?>" class="link1 bOrangeButton<?php if ($disable): ?> disable<? endif ?>"><i></i><span>Положить в корзину</span></a>

<? else: ?>
    <a href="<?= $url ?>" class="link1 bOrangeButton active<?php if ($disable): ?> disable<? endif ?>"><i></i><span>В корзине</span></a>

<? endif ?>