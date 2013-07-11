<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\Entity
 * @var $disabled bool
 * @var $value    string
 * @var $gaEvent  string
 * @var $gaTitle  string
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
$btnText = $inCart ? 'В корзине' : 'В корзину';
?>

<?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => $btnText]) // Кнопка купить ?>
