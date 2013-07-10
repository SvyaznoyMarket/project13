<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 */
?>

<div class="bWidgetBuy mWidget">
    <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId())]) ?>

    <?= $page->render('cart/_button', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'В корзину', 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) // Кнопка купить ?>

    <?= $helper->render('product/__oneClick', ['product' => $product]) // Покупка в один клик ?>

    <?= $helper->render('product/__delivery', ['product' => $product]) // Доставка ?>

    <div class="bAwardSection"><img src="/css/newProductCard/img/award.jpg" alt="" /></div>
</div><!--/widget delivery -->

<?//= $helper->render('product/__warranty', ['product' => $product]) ?>

<?//= $helper->render('product/__service', ['product' => $product]) ?>