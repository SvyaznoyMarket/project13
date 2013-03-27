<?php
/**
 * @var $page    \View\Product\StockPage
 * @var $user    \Session\User
 * @var $product \Model\Product\Entity
 */
?>

<h1>Вы добавили в корзину</h1>
<div class="clear"></div>

<div class="bUpsale clearfix">
    <div class="bUpsaleGoodBlock fl">
        <div class="clearfix">
            <img class="bUpsaleGoodBlock__eImg fl" src="<?= $product->getImageUrl(1) ?>" width="83" height="83" alt="<?=$product->getName()?>"/>
            <div class="fl pt20">
                <h2 class="bUpsaleGoodBlock__eTitle"><?=$product->getName()?></h2>
                <div class="bUpsaleGoodBlock__ePrice"><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>
            </div>
        </div>
        <a class="bUpsaleGoodBlock__eBackLink" href="#">< Продолжить покупки</a>
    </div>

    <div class="bUpsaleRedirectBlock fr">
        <h2 class="bUpsaleRedirectBlock__eTitle">В корзине <strong class="bUpsaleRedirectBlock__eCounter">2</strong> товара на сумму <strong class="bUpsaleRedirectBlock__ePrice">25 000 <span class="rubl">p</span></strong></h2>
        <div class="clearfix">
            <a class="bUpsaleRedirectBlock__eLink fl" href="">Перейти в корзину</a>
            <a class="bUpsaleRedirectBlock__eLink fr mFullOrange" href="">Оформить заказ ></a>
        </div>
    </div>
</div>

<? if ($showAccessoryUpper && (bool)$accessories && \App::config()->product['showAccessories']): ?>
    <?= $page->render('product/_slider', array('product' => $product, 'productList' => array_values($accessories), 'totalProducts' => count($product->getAccessoryId()), 'itemsInSlider' => \App::config()->product['itemsInSlider'], 'page' => 1, 'title' => 'Аксессуары', 'url' => $page->url('product.accessory', array('productToken' => $product->getToken())), 'gaEvent' => 'Accessorize')) ?>
<? endif ?>