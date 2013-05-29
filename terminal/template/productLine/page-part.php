<?php
/**
 * @var $page        \Terminal\View\ProductLine\PartPage
 * @var $line        \Model\Line\Entity
 * @var $partsById   \Model\Product\TerminalEntity[]
 * @var $mainProduct \Model\Product\TerminalEntity
 */
?>

<article id="categoryData" class="bListing bContent mLine" data-pagetype='product_model_list'>
	<div class="bLinePart mModelList">
		<div class="bLinePart__eMainInfo">
			<a class="bProductListItem__eImgLink" data-screentype='product_set' data-lineid='' href="#"><img class="bProductListItem__eImg" src="<?= $mainProduct ? $mainProduct->getImageUrl(3) : '' ?>" alt="" /></a>
			<h1 class="bProductListItem__eTitle">Серия <?= $line->getName() ?></h1>
			<ul class="bProductListItem__eAboutBlock">
				<li class="bProductListItem__eAboutBlockItem mListDisk">Наборов: <?= count($line->getKitId()) ?></li>
				<li class="bProductListItem__eAboutBlockItem mListDisk">Предметов: <?= count($line->getProductId()) ?></li>
			</ul>
			<a class="bProductListItem__eBtn bButton mOrangeBtn jsRedirect" data-screentype='product_set' data-lineid='' href="#">смотреть наборы</a>
			<p class="bProductListItem__eF1Block">Доставит и соберет</p>
		</div>
		<div class="bLinePart__eParts">
			<h2 class="bLinePart__ePartsTitle bProductListItem__eTitle">Собрать свой набор</h2>
			<div class="bGoodItemKit">
                <div class="clearfix">

                    <? foreach ($partsById as $part): ?>
                    <div class="bGoodSubItem_eGoods bGoodItemKit_eItem mLineParts mMB20 mFl">
                        <a class="bGoodSubItem_eGoodsImg mFl mRounded jsRedirect" href="#" data-screentype='product' data-productid='<?= $part->getId() ?>'>
                            <? if ($part->getLabel()): ?>
                                <img class="bLabels" src="<?= $part->getLabel()->getImageUrl(1) ?>" alt="" height="20" />
                            <? endif ?>
                            <img width="130" height="130" src="<?= $part->getImageUrl(1) ?>"/>
                        </a>
                        <div class="bGoodSubItem_eGoodsInfo">
                            <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink jsRedirect" href="#" data-screentype='product' data-productid='<?= $part->getId() ?>'><?= $part->getName() ?></a></h2>
                            <p class="bGoodSubItem_ePrice">2000 <?= $page->helper->formatPrice($part->getPrice()) ?> <span class="bRuble">p</span></p>
                            <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn jsBuyButton" href="#" data-productid='<?= $part->getId() ?>'>в корзину</a>
                        </div>
                    </div>
                    <? endforeach ?>

                </div>
            </div>
		</div>
	</div>
</article>