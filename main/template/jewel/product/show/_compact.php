<?php
/**
 * @var $page         \View\Layout
 * @var $product      \Model\Product\Entity
 * @var $addInfo      array
 * @var $itemsPerRow  int
 * @var $productVideo \Model\Product\Video\Entity|null
 * @var $view         array
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

<li class="bBrandGoodsList__eItem <? if ($view['descriptionHover']): ?>bBrandGoodsList__eItem-descriptionHover<? endif ?> js-jewelListing">
    <div class="goodsbox" ref="<?= $product->getToken(); ?>"><? //для корректной работы js ?>
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?> <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>
        <? if ('top' === $view['descriptionPosition']): ?>
            <div class="bItemName"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
        <? endif ?>

        <div class="bItemImg"><a href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl($imgSize) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" /></a></div>

            <ul class="bSimplyDescStikers clearfix">
                <? if ($product->getLabel() && $product->getLabel()->getImageUrl()): ?>
                   <li class="bSimplyDescStikers__eItem mLeftStiker"><img class="SimplyDescStikers__eImg" src="<?= $page->escape($product->getLabel()->getImageUrl()) ?>" alt="<?= $page->escape($product->getLabel()->getName()) ?>" /></li>
                <? endif ?>

                <? if ($product->getBrand() && $product->getBrand()->getImage()): ?>
                    <li class="bSimplyDescStikers__eItem mRightStiker"><img class="SimplyDescStikers__eImg" src="<?= $page->escape($product->getBrand()->getImage()) ?>" alt="<?= $page->escape($product->getBrand()->getName()) ?>" /></li>
                <? endif ?>

                <? if ($productVideo && $productVideo->getContent()): ?>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/video.png" /></li>
                <? endif ?>

                <? if ($model3dExternalUrl || $model3dImg): ?>
                    <li class="bSimplyDescStikers__eItem"><img class="SimplyDescStikers__eImg" src="/css/bCatalog/img/grad360.png" /></li>
                <? endif ?>
            </ul>

            <? if ($product->getPriceOld()): ?>
                <div class="bPriceLine clearfix">
                    <span class="bPriceOld">
                        <strong class="bDecor"><?= $helper->formatPrice($product->getPriceOld()) ?></strong> <span class="rubl">p</span>
                    </span>
                </div>

                <? if ($priceSale): ?>
                    <span class="bPriceSale">-<?= $priceSale ?>%</span>
                <? endif ?>
            <? endif ?>

            <div class="bItemPrice"><span><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

            <? if ($product->getIsBuyable()): ?>
                <?= $helper->render('cart/__button-product', ['product' => $product]) // Кнопка купить ?>
            <? endif ?>

            <a href="" class="btnCmprb jsCompareListLink" data-id="<?= $page->escape($product->getId()) ?>" data-bind="compareListBinding: compare"></a>

            <? if ('bottom' === $view['descriptionPosition']): ?>
                <div class="bItemName"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>

                <? if (\App::config()->product['reviewEnabled']): ?>
                    <?= $page->render('product/_reviewsStarsCompact', ['product' => $product]) ?>
                <? endif ?>
            <? endif ?>
        </div>

        <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
    </div>
</li>
