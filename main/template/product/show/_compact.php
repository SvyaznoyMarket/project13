<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\CompactEntity
 * @var $isHidden      bool
 * @var $kit           \Model\Product\Kit\Entity
 * @var $productVideos \Model\Product\Video\Entity[]
 * @var $addInfo       array
 **/
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
if (!isset($productVideos)) $productVideos = [];
$addInfo = isset($addInfo)?$addInfo:[];

/** @var $productVideo \Model\Product\Video\Entity|null */
$productVideo = reset($productVideos);
?>

<style type="text/css">
    .goodsbox .photo .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small,
    .goodsbox .photo .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small:hover {
        background: url('/css/item/img/videoStiker_small.png') no-repeat 0 0;
        right: 0;
        top: 130px;
        width: 42px;
        height: 35px;
    }
</style>
<div class="goodsbox"<? if ($isHidden): ?> style="display:none;"<? endif ?> ref="<?= $product->getToken(); ?>">
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (count($addInfo)) print 'data-add="'.$page->json($addInfo).'"'; ?>>
    	<div class="photo">
            <? if ($productVideo && $productVideo->getContent()): ?><a class="goodsphoto_eVideoShield goodsphoto_eVideoShield_small" href="<?= $product->getLink() ?>"></a><? endif ?>
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
        
        <?= $page->render('product/_reviewsStarsCompact', ['product' => $product]) ?>

	    <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
        <div class="goodsbar mSmallBtns mR">
            <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
        </div>
	    <div class="font18 pb10 mSmallBtns">
            <? if ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
            <p class="font16 crossText"><span class="old_price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></p>
            <? endif ?>
            <span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span>
        </div>
        <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
        	<div class="notBuying font12">
                <div class="corner"><div></div></div>
                Только в магазинах
            </div>
		<? endif ?>
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