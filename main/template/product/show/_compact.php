<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\Entity
 * @var $isHidden      bool
 * @var $kit           \Model\Product\Kit\Entity
 * @var $addInfo       array
 **/
//print_r($addInfo);
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
?>

<div class="goodsbox <? echo ($isHidden)? 'hidden': '' ?> js-goodsbox">
    <div class="goodsbox__inner js-goodsboxContainer" data-url="<?= $product->getLink() ?>" <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>
    	<div class="photo">
            <? if ($product->hasVideo()): ?>
                <a class="goodsphoto_eVideoShield goodsphoto_eVideoShield_small" href="<?= $product->getLink() ?>"></a>
            <? endif ?>

            <? if ($product->has3d()): ?>
                <a style="right:<?= $product->hasVideo() ? '42' : '0' ?>px;" class="goodsphoto_eGrad360 goodsphoto_eGrad360_small" href="<?= $product->getLink() ?>"></a>
            <? endif ?>

	        <a href="<?= $product->getLink() ?>">
	            <? if (!empty($kit) && $kit->getCount()): ?>
	                <div class="bLabelsQuantity" src="/images/quantity_shild.png"><?= $kit->getCount() ?> шт.</div>
	            <? endif ?>
	
	            <? if ($label = $product->getLabel()): ?>
	                <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $page->escape($label->getName()) ?>"/>
	            <? endif ?>
	
	            <img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" title="<?= $page->escape($product->getNameWithCategory()) ?>" width="160" height="160"/>
	        </a>
	    </div>

        <? if (\App::config()->product['reviewEnabled']): ?>
            <?= $page->render('product/_reviewsStarsCompact', ['product' => $product]) ?>
        <? endif ?>

	    <div class="h3"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>

        <div class="font18 pb10 mSmallBtns">
            <? if ($product->getPriceOld()): ?>
                <p class="font16 crossText"><span class="old_price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></p>
            <? endif ?>
            <span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span>
        </div>
        <div class="bBtnLine clearfix">
            <? if (!$product->getKit() || $product->getIsKitLocked()) : ?>
                <?= $page->render('cart/_button', ['product' => $product]) ?>
                <a href="" class="btnCmprb jsCompareListLink" data-id="<?= $page->escape($product->getId()) ?>" data-bind="compareListBinding: compare" data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>" data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>"></a>
            <? endif; ?>

            <a class="btnView btn1" href="<?= $product->getLink() ?>">Посмотреть</a>
        </div>

        <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
	    <? if ($hasModel): ?>
        <a href="<?= $product->getLink() ?>">
            <div class="bListVariants">
                Доступно в разных вариантах<br>
                (<?= $product->getModel()->getVariations() ?>)
            </div>
        </a>
        <? endif ?>
    </div>
</div>