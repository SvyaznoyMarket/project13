<?php
/**
 * @var $page         \View\Layout
 * @var $product      \Model\Product\Entity
 * @var $addInfo      array
 * @var $itemsPerRow  int
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
if (!isset($addInfo)) {
    $addInfo = [];
}

$disabled = !$product->getIsBuyable();
$gaEvent = !empty($gaEvent) ? $gaEvent : null;
$gaTitle = !empty($gaTitle) ? $gaTitle : null;
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.set', array('productId' => $product->getId()));
}

// открытие товаров в новом окне
$linkTarget = \App::abTest()->isNewWindow() ? ' target="_blank" ' : '';

if ($product->getPriceOld()) {
    $priceSale = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
} else {
    $priceSale = 0;
}
?>

<li class="lstn_i js-jewelListing js-goodsbox">
    <div class="lstn_i_inn js-goodsboxContainer" data-url="<?= $product->getLink() ?>" <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>

        <a class="lstn_n" href="<?= $product->getLink() ?>" <?= $linkTarget ?>><?= $product->getName() ?></a>

        <a class="lstn_imglk" href="<?= $product->getLink() ?>" <?= $linkTarget ?>><img class="lstn_img" src="<?= $product->getMainImageUrl(3 == $itemsPerRow ? 'product_350' : 'product_160') ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" /></a>

        <ul class="stickLst clearfix">
            <? if ($product->getLabel() && $product->getLabel()->getImageUrl()): ?>
               <li class="stickLst_i stickLst_i-l"><img class="stickLst_img" src="<?= $page->escape($product->getLabel()->getImageUrl()) ?>" alt="<?= $page->escape($product->getLabel()->getName()) ?>" /></li>
            <? endif ?>

            <? if ($product->getBrand() && $product->getBrand()->getImage()): ?>
                <li class="stickLst_i stickLst_i-r"><img class="stickLst_img" src="<?= $page->escape($product->getBrand()->getImage()) ?>" alt="<?= $page->escape($product->getBrand()->getName()) ?>" /></li>
            <? endif ?>

            <? if ($product->hasVideo()): ?>
                <li class="stickLst_i"><img class="stickLst_img" src="/css/bCatalog/img/video.png" /></li>
            <? endif ?>

            <? if ($product->has3d()): ?>
                <li class="stickLst_i"><img class="stickLst_img" src="/css/bCatalog/img/grad360.png" /></li>
            <? endif ?>
        </ul>

        <div class="lstn_pr">
            <? if ($product->getPriceOld()): ?>
                <span class="lstn_pr_old">
                    <span class="td-lineth"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>

                    <? if ($priceSale): ?>
                        &nbsp;<span class="lstn_pr_sale">-<?= $priceSale ?>%</span>
                    <? endif ?>
                </span>
            <? endif ?>

            <?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span>
        </div>

        <div class="lstn_btn">
            <a href="" class="btnCmprb jsCompareListLink" data-id="<?= $page->escape($product->getId()) ?>" data-bind="compareListBinding: compare" data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>" data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>"></a>

            <? if ($product->getIsBuyable()): ?>
                <?= $helper->render('cart/__button-product', ['product' => $product]) // Кнопка купить ?>
            <? endif ?>
        </div>
    </div>

    <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
</li>
