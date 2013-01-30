<?php
/**
 * @var $page    \Terminal\View\Product\IndexPage
 * @var $product \Model\Product\Entity
 * @var $related \Model\Product\Entity[]
 */
?>

<article class="bGoodItem">
    <h1><?= $product->getName() ?></h1>

    <img class="bGoodItem_eMainImg" src="<?= $product->getImageUrl(3) ?>" alt="<?= $page->escape($product->getName()) ?>"/>
    <? foreach ($product->getPhoto() as $photo): ?>
        <img class="bGoodItem_ePreviewImg" src="<?= $photo->getUrl(2) ?>" alt="<?= $page->escape($product->getName()) ?>"/>
    <? endforeach ?>

    <p class="bGoodItem_eRating"><?= $product->getRating() ?></p>

    <p class="bGoodItem_eShortDesc"><?= $product->getTagline() ?></p>

    <p class="bGoodItem_ePrice"><?= $page->helper->formatPrice($product->getPrice()) ?></p>

    <p><a class="bGoodItem_eBayBtn" href="#" onclick="terminal.cart.addProduct(<?= $product->getId() ?>)">Купить</a></p>
    <hr/>
    <div class="bGoodSimilar">
        <? foreach ($related as $iProduct): ?>
        <div class="bGoodSimilar_eGoods">
            <a class="bGoodSimilar_eGoodsImg" href="#link"><img width="83" height="83" src="<?= $iProduct->getImageUrl(1) ?>"/></a>
            <div class="bGoodSimilar_eGoodsInfo">
                <p class="bGoodSimilar_eRating"><?= $iProduct->getRating() ?></p>
                <h2><a href="#link"><?= $iProduct->getName() ?></a></h2>
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