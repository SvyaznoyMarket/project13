<?php

/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 */

$shop = $productFilter->getShop()?$productFilter->getShop():null;

if (!$page->hasGlobalParam('shops')) {
    $shops = null;
} else $shops = $page->getGlobalParam('shops');
?>

<? if ($shops) : ?>
    <dt>Наличие в магазинах</dt>
    <dd style="display: <?= $shop ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <li onclick="document.location='<?=$page->url('product.category.shop', ['categoryPath' => $category->getPath(), 'shopid' => 0 ])?>';">
            <input name="shop" id="shop_0" type="radio" class="hiddenCheckbox"/>
            <label for="shop_0" class="prettyCheckbox radio list <?=$shop?'':'checked';?> ">
                <span class="holderWrap" style="width: 13px; height: 13px;">
                    <span class="holder" style="width: 13px; "></span>
                </span>
                Не важно
            </label>
        </li>
        <? foreach ($shops as $i => $singleShop) { ?>
            <li onclick="document.location='<?=$page->url('product.category.shop', ['categoryPath' => $category->getPath(), 'shopid' => $singleShop->getId() ])?>';">
                <input name="shop" id="shop_<?=$i+1?>" type="radio" class="hiddenCheckbox"/>
                <label for="shop_<?=$i+1?>" class="prettyCheckbox radio list <?=($shop&&$shop->getId() == $singleShop->getId())?'checked':'';?>">
                    <span class="holderWrap" style="width: 13px; height: 13px;">
                        <span class="holder" style="width: 13px; "></span>
                    </span>
                    <?=$singleShop->getAddress();?>
                </label>
            </li>
        <? } ?>
    </ul>
    </dd>
<? endif ?>