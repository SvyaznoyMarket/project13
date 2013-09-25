<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\Entity
 * @var $disabled bool
 */
?>

<?php
$helper = new \Helper\TemplateHelper();
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.set', array('productId' => $product->getId()));
}
$inCart = \App::user()->getCart()->hasProduct($product->getId());
$btnText = $inCart ? 'В корзине' : 'Купить';
?>

<?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => $btnText]) // Кнопка купить ?>
