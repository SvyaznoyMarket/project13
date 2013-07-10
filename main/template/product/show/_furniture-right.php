<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 */
?>

<div class="bWidgetBuy mWidget">
    <div class="bStoreDesc">
        <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

        <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

        <?= $helper->render('product/__notification-lowerPrice', ['product' => $product]) // Узнать о снижении цены ?>

        <?//= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Беру в кредит ?>
    </div>

    <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'В корзину']) // Кнопка купить ?>

    <div id="coupeError" class="red" style="display:none"></div>

    <?= $helper->render('product/__oneClick', ['product' => $product]) // Покупка в один клик ?>

    <?= $helper->render('product/__delivery', ['product' => $product]) // Доставка ?>

    <div class="bAwardSection"><img src="/css/newProductCard/img/award.jpg" alt="" /></div>
</div><!--/widget delivery -->