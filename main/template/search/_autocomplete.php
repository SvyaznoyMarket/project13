<?php
    /**
     * @var $searchQuery      string
     * @var $products         \Model\Search\Product\Entity[]
     * @var $categories       \Model\Search\Category\Entity[]
     */
?>
<div class="bSearchSuggest">
    <? if ((bool)$categories): ?>
    <div class="bSearchSuggest__eCategoryList">
        <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Категория</span></p>
        <? foreach ($categories as $category): ?>
        <a class="bSearchSuggest__eCategoryRes bSearchSuggest__eRes" href="<?= $category->getLink() ?>">
            <?= preg_replace('/'.$searchQuery.'/iu', '<span class="bSearchSuggest__eSelected">' . htmlspecialchars('$0', ENT_QUOTES, \App::config()->encoding) . '</span>', $category->getName()) ?>
        </a>
        <? endforeach ?>
    </div>
    <? endif ?>
    <? if ((bool)$products): ?>
    <div class="bSearchSuggest__eProductList">
        <p class="bSearchSuggest__eListLine"><span class="bSearchSuggest__eListTitle">Товар</span></p>
        <? foreach ($products as $product): ?>
        <a class="bSearchSuggest__eGoodRes bSearchSuggest__eRes clearfix" href="<?= $product->getLink()?>">
            <img class="bSearchSuggest__eGoodImgRes fl" src="<?= $product->getImageUrl(1) ?>" width="48" height="48"/>
            <p class="bSearchSuggest__eGoodTitleRes fl"><?= preg_replace('/'.$searchQuery.'/iu', '<span class="bSearchSuggest__eSelected">' . htmlspecialchars('$0', ENT_QUOTES, \App::config()->encoding) . '</span>', $product->getName()) ?></p>
        </a>
        <? endforeach ?>
    </div>
    <? endif ?>
</div>
