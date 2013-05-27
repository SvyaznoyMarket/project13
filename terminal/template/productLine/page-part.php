<?php
/**
 * @var $page  \Terminal\View\ProductLine\PartPage
 * @var $line  \Model\Product\Line\Entity
 * @var $parts \Model\Product\Line\Entity[]
 */
?>

<article id="categoryData" class="bListing bContent mLine" data-pagetype='product_model_list'>
	<div class="bLinePart mModelList">
		<div class="bLinePart__eMainInfo">
			<a class="bProductListItem__eImgLink" data-screentype='product_set' data-lineid='' href="#"><img class="bProductListItem__eImg" src="" alt="" /></a>
			<h1 class="bProductListItem__eTitle">Набор клевый</h1>
			<ul class="bProductListItem__eAboutBlock">
				<li class="bProductListItem__eAboutBlockItem mListDisk">Наборов: 1</li>
				<li class="bProductListItem__eAboutBlockItem mListDisk">Предметов: 2</li>
			</ul>
			<a class="bProductListItem__eBtn bButton mOrangeBtn jsRedirect" data-screentype='product_set' data-lineid='' href="#">смотреть наборы</a>
			<p class="bProductListItem__eF1Block">Доставит и соберет</p>
		</div>
		<div class="bLinePart__eParts">
			<h2 class="bLinePart__ePartsTitle bProductListItem__eTitle">Собрать свой набор</h2>
			<div class="bGoodItemKit">
                <div class="clearfix">
                    <? //foreach ($product->getKit() as $part): ?>
                    <div class="bGoodSubItem_eGoods bGoodItemKit_eItem mLineParts mMB20 mFl">
                        <a class="bGoodSubItem_eGoodsImg mFl mRounded jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>
                            <? // if ($kit[$part->getId()]->getLabel()): ?>
                                <img class="bLabels" src="<?//= $kit[$part->getId()]->getLabel()->getImageUrl(1) ?>" alt="<?//= $kit[$part->getId()]->getLabel()->getName() ?>" height="20" />
                            <? //endif ?>
                            <img width="130" height="130" src="<?//= $kit[$part->getId()]->getImageUrl(1) ?>"/>
                        </a>
                        <div class="bGoodSubItem_eGoodsInfo">
                            <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>Какая-то запчасть<?//= $kit[$part->getId()]->getName() ?></a></h2>
                            <p class="bGoodSubItem_ePrice">2000 <?//= $page->helper->formatPrice($kit[$part->getId()]->getPrice()) ?> <span class="bRuble">p</span></p>
                            <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn jsBuyButton" href="#" data-productid='<?//= $part->getId() ?>'>в корзину</a>
                        </div>
                    </div>
                    <?// endforeach ?>
                    <div class="bGoodSubItem_eGoods bGoodItemKit_eItem mLineParts mMB20 mFl">
                        <a class="bGoodSubItem_eGoodsImg mFl mRounded jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>
                            <? // if ($kit[$part->getId()]->getLabel()): ?>
                                <img class="bLabels" src="<?//= $kit[$part->getId()]->getLabel()->getImageUrl(1) ?>" alt="<?//= $kit[$part->getId()]->getLabel()->getName() ?>" height="20" />
                            <? //endif ?>
                            <img width="130" height="130" src="<?//= $kit[$part->getId()]->getImageUrl(1) ?>"/>
                        </a>
                        <div class="bGoodSubItem_eGoodsInfo">
                            <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>Какая-то запчасть<?//= $kit[$part->getId()]->getName() ?></a></h2>
                            <p class="bGoodSubItem_ePrice">2000 <?//= $page->helper->formatPrice($kit[$part->getId()]->getPrice()) ?> <span class="bRuble">p</span></p>
                            <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn jsBuyButton" href="#" data-productid='<?//= $part->getId() ?>'>в корзину</a>
                        </div>
                    </div>
                    <div class="bGoodSubItem_eGoods bGoodItemKit_eItem mLineParts mMB20 mFl">
                        <a class="bGoodSubItem_eGoodsImg mFl mRounded jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>
                            <? // if ($kit[$part->getId()]->getLabel()): ?>
                                <img class="bLabels" src="<?//= $kit[$part->getId()]->getLabel()->getImageUrl(1) ?>" alt="<?//= $kit[$part->getId()]->getLabel()->getName() ?>" height="20" />
                            <? //endif ?>
                            <img width="130" height="130" src="<?//= $kit[$part->getId()]->getImageUrl(1) ?>"/>
                        </a>
                        <div class="bGoodSubItem_eGoodsInfo">
                            <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>Какая-то запчасть<?//= $kit[$part->getId()]->getName() ?></a></h2>
                            <p class="bGoodSubItem_ePrice">2000 <?//= $page->helper->formatPrice($kit[$part->getId()]->getPrice()) ?> <span class="bRuble">p</span></p>
                            <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn jsBuyButton" href="#" data-productid='<?//= $part->getId() ?>'>в корзину</a>
                        </div>
                    </div>
                    <div class="bGoodSubItem_eGoods bGoodItemKit_eItem mLineParts mMB20 mFl">
                        <a class="bGoodSubItem_eGoodsImg mFl mRounded jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>
                            <? // if ($kit[$part->getId()]->getLabel()): ?>
                                <img class="bLabels" src="<?//= $kit[$part->getId()]->getLabel()->getImageUrl(1) ?>" alt="<?//= $kit[$part->getId()]->getLabel()->getName() ?>" height="20" />
                            <? //endif ?>
                            <img width="130" height="130" src="<?//= $kit[$part->getId()]->getImageUrl(1) ?>"/>
                        </a>
                        <div class="bGoodSubItem_eGoodsInfo">
                            <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink jsRedirect" href="#" data-screentype='product' data-productid='<?//= $part->getId() ?>'>Какая-то запчасть<?//= $kit[$part->getId()]->getName() ?></a></h2>
                            <p class="bGoodSubItem_ePrice">2000 <?//= $page->helper->formatPrice($kit[$part->getId()]->getPrice()) ?> <span class="bRuble">p</span></p>
                            <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn jsBuyButton" href="#" data-productid='<?//= $part->getId() ?>'>в корзину</a>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</article>