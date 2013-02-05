<?php
/**
 * @var $page    \Terminal\View\Product\IndexPage
 * @var $product \Model\Product\Entity
 * @var $related \Model\Product\Entity[]
 */
?>

<article class="bGoodItem">    
    
    <div class="bGoodItemHead mRounded mBlackBlock clearfix">
        <div class="bGoodImgBlock mRounded mFl mW940">
            <div class="bPreviewImg">
                <? foreach ($product->getPhoto() as $photo): ?>
                    <a class="bPreviewImg_eLink mRounded" href="">
                        <img class="bPreviewImg_eImage" src="<?= $photo->getUrl(2) ?>" alt="<?= $page->escape($product->getName()) ?>"/>
                    </a>
                <? endforeach ?>
            </div>
            <div class="bGoodImgBlock_eMainImg">
                <? if ($product->getLabel()): ?>
                    <img class="bLabels" src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $product->getLabel()->getName() ?>" />
                <? endif ?>
                <img width="480" src="<?= $product->getImageUrl(3) ?>" alt="<?= $page->escape($product->getName()) ?>"/>
            </div>
        </div>
        
        <div class="bGoodDescBlock mFr mW600">
            <div class="clearfix">
                <p class="mFl">Код товара:<?= $product->getArticle() ?></p>
                <p class="mFr"><span class="bRating mRate_<?= $product->getRating() ?>"><?= $product->getRating() ?></span></p>
            </div>
            <h1 class="bTitle"><?= $product->getName() ?></h1>
        
            <div class="bGoodDescBlock_eSubBlock">
                <p class="bGoodDescBlock_ePrice"><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="bRuble">p</span></p>
            </div>

            <div class="clearfix">
                <a class="bGoodDescBlock_eBayBtn bButton mOrangeBtn mFl" href="#" onclick="terminal.cart.addProduct(<?= $product->getId() ?>)">В корзину</a>
                <a class="bGoodDescBlock_eCompBtn bButton mGrayBtn mFl" href="#">К сравнению</a>
            </div>

            <p class="bGoodDescBlock_eShortDesc"><?= $product->getTagline() ?></p>
        </div>
    </div>


    <hr/>
    <div class="bGoodSimilar">
        <? foreach ($related as $iProduct): ?>
        <div class="bGoodSimilar_eGoods">
            <a class="bGoodSimilar_eGoodsImg" href="#link" onclick="terminal.core.gui.screen.push('product', {productId: <?= $iProduct->getId() ?>})"><img width="83" height="83" src="<?= $iProduct->getImageUrl(1) ?>"/></a>
            <div class="bGoodSimilar_eGoodsInfo">
                <p class="bGoodSimilar_eRating"><?= $iProduct->getRating() ?></p>
                <h2><a href="#link" onclick="terminal.core.gui.screen.push('product', {productId: <?= $iProduct->getId() ?>})"><?= $iProduct->getName() ?></a></h2>
                <p class="bGoodSimilar_ePrice"><?= $page->helper->formatPrice($iProduct->getPrice()) ?></p>
            </div>
        </div>
        <? endforeach ?>
    </div>
    <hr/>
    <p class="bGoodItem_eFullDesc"><?= $product->getDescription() ?></p>
    <hr/>
    <div class="bGoodItem_eSpecification">
        <? foreach ($product->getGroupedProperties() as $group): ?>
        <div class="bGoodSpecification">
            <h3 class="bGoodSpecification_eBlockName"><?= $group['group']->getName() ?></h3>
            <? foreach ($group['properties'] as $property): ?>
			    <p><span class="bGoodSpecification_eSpecTitle"><?= $property->getName() ?></span> - <span class="bGoodSpecification_eSpecValue"><?= $property->getStringValue() ?></span></p>
			<? endforeach ?>
        </div>
        <? endforeach ?>
    </div>
</article>