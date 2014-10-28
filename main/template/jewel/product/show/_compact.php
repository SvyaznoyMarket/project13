<?php
/**
 * @var $page         \View\Layout
 * @var $product      \Model\Product\Entity
 * @var $addInfo      array
 * @var $itemsPerRow  int
 * @var $productVideo \Model\Product\Video\Entity|null
 **/
?>

<?
if ($_SERVER['APPLICATION_ENV'] === 'local') {
    $product->setPriceOld(10000);
}

$helper = new \Helper\TemplateHelper();
$disabled = !$product->getIsBuyable();
$gaEvent = !empty($gaEvent) ? $gaEvent : null;
$gaTitle = !empty($gaTitle) ? $gaTitle : null;
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.set', array('productId' => $product->getId()));
}

$imgSize = isset($itemsPerRow) && 3 == $itemsPerRow ? 6 : 2;
if ($product->getPriceOld()) {
    $priceSale = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
} else {
    $priceSale = 0;
}

if ($productVideo instanceof \Model\Product\Video\Entity) {
    $model3dExternalUrl = $productVideo->getMaybe3d();
    $model3dImg = $productVideo->getImg3d();
} else {
    $model3dExternalUrl = '';
    $model3dImg = '';
}
?>

<li class="brandItems_i js-jewelListing">
    <div class="goodsbox" ref="<?= $product->getToken(); ?>"><? //для корректной работы js ?>
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?> <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>

        <a class="brandItems_n" href="<?= $product->getLink() ?>"><?= $product->getName() ?></a>

        <a class="brandItems_img" href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl($imgSize) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" /></a>

        <ul class="stickLst clearfix">
            <? if ($product->getLabel() && $product->getLabel()->getImageUrl()): ?>
               <li class="stickLst_i stickLst_i-l"><img class="stickLst_img" src="<?= $page->escape($product->getLabel()->getImageUrl()) ?>" alt="<?= $page->escape($product->getLabel()->getName()) ?>" /></li>
            <? endif ?>

            <? if ($product->getBrand() && $product->getBrand()->getImage()): ?>
                <li class="stickLst_i stickLst_i-r"><img class="stickLst_img" src="<?= $page->escape($product->getBrand()->getImage()) ?>" alt="<?= $page->escape($product->getBrand()->getName()) ?>" /></li>
            <? endif ?>

            <? if ($productVideo && $productVideo->getContent()): ?>
                <li class="stickLst_i"><img class="stickLst_img" src="/css/bCatalog/img/video.png" /></li>
            <? endif ?>

            <? if ($model3dExternalUrl || $model3dImg): ?>
                <li class="stickLst_i"><img class="stickLst_img" src="/css/bCatalog/img/grad360.png" /></li>
            <? endif ?>
        </ul>
            
        <div class="lstn_pr">
            <? if ($product->getPriceOld()): ?>
                <span class="lstn_pr_old">
                    <?= $helper->formatPrice($product->getPriceOld()) ?> <span class="rubl">p</span>

                    <? if ($priceSale): ?>
                        &nbsp;<span class="lstn_pr_sale">-<?= $priceSale ?>%</span>
                    <? endif ?>
                </span>
            <? endif ?>

            <?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span>
        </div>

        <div class="lstn_btn">
            <a href="" class="btnCmprb jsCompareListLink" data-id="<?= $page->escape($product->getId()) ?>" data-bind="compareListBinding: compare"></a>

            <? if ($product->getIsBuyable()): ?>
                <?= $helper->render('cart/__button-product', ['product' => $product]) // Кнопка купить ?>
            <? endif ?>
        </div>
    </div>

    <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
    </div>
</li>
