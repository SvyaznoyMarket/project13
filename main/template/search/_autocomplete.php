<?php
    /**
     * @var $searchQuery      string
     * @var $products         \Model\Search\Product\Entity[]
     * @var $categories       \Model\Search\Category\Entity[]
     */
?>
<?
$pattern = '/' . preg_replace('/\s+/', '|', preg_quote($searchQuery, '/')) . '/iu';
?>
<div class="bSearchSuggest">
    <? if ((bool)$categories): ?>
    <div class="bSearchSuggest__eCategoryList">
        <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Категории</span></p>
        <? foreach ($categories as $category): ?>
        <a class="bSearchSuggest__eCategoryRes bSearchSuggest__eRes jsSearchSuggestCategory" href="<?= $category->getLink() ?>">
            <?= preg_replace($pattern, '<span class="bSearchSuggest__eSelected">' . htmlspecialchars('$0', ENT_QUOTES, \App::config()->encoding) . '</span>', $category->getName()) ?>
        </a>
        <? endforeach ?>
    </div>
    <? endif ?>
    <? if ((bool)$products): ?>
    <div class="bSearchSuggest__eProductList">
        <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Товары</span></p>
        <? foreach ($products as $product): ?>
        <a class="bSearchSuggest__eGoodRes bSearchSuggest__eRes clearfix jsSearchSuggestProduct" href="<?= $product->getLink()?>">
            <img class="bSearchSuggest__eGoodImgRes fl" src="<?= $product->getImageUrl(1) ?>" width="48" height="48"/>
            <p class="bSearchSuggest__eGoodTitleRes fl"><?= preg_replace($pattern, '<span class="bSearchSuggest__eSelected">' . htmlspecialchars('$0', ENT_QUOTES, \App::config()->encoding) . '</span>', $product->getName()) ?></p>
        </a>
        <? endforeach ?>
    </div>
    <? endif ?>
</div>