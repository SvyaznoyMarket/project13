<?php
/**
 * @var $page \View\Layout
 * @var $product \Model\Product\Entity|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];

if ($product) {
    $links[] = ['name' => $product->getPrefix(), 'url' => $product->getParentCategory() ? $product->getParentCategory()->getLink() : null, 'last' => false];
    $links[] = ['name' => $product->getWebName(), 'url' => null, 'last' => true];
} ?>

<div class="fixedTopBar__crumbs">
    <div class="fixedTopBar__crumbsImg"><img class="crumbsImg" src="<?= $product ? $product->getImageUrl() : '' ?>" /></div>

    <div class="wrapperCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
        <div class="transGradWhite"></div>
    </div>
</div>

<div class="fixedTopBar__buy">
    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

    <div class="bCountSection clearfix" data-spinner-for="">
        <button class="bCountSection__eM">-</button>
        <input class="bCountSection__eNum" type="text" value="1">
        <button class="bCountSection__eP">+</button>
        <span>шт.</span>
    </div><!--/counter -->

    <div class="bWidgetBuy__eBuy btnBuy">
        <a href="" class="btnBuy__eLink jsBuyButton" data-group="">Купить</a>
    </div>
</div>