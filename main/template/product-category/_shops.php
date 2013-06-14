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

<dt>Наличие в магазинах</dt>
<dd style="display: <?= $shop ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <li onclick="document.location='<?=$page->url('product.category.shop', ['categoryPath' => $category->getPath(), 'shopid' => 0 ])?>';">
            <input name="" type="radio" class="hiddenCheckbox"/>
            <label for="" class="prettyCheckbox radio list <?=$shop?'':'checked';?> ">
                <span class="holderWrap" style="width: 13px; height: 13px;">
                    <span class="holder" style="width: 13px; "></span>
                </span>
                Неважно
            </label>
        </li>
        <?
        foreach ($shops as $singleShop) {
            ?>
            <li onclick="document.location='<?=$page->url('product.category.shop', ['categoryPath' => $category->getPath(), 'shopid' => $singleShop->getId() ])?>';">
                <input name="" type="radio" class="hiddenCheckbox"/>
                <label for="" class="prettyCheckbox radio list <?=($shop&&$shop->getId() == $singleShop->getId())?'checked':'';?>">
                    <span class="holderWrap" style="width: 13px; height: 13px;">
                        <span class="holder" style="width: 13px; "></span>
                    </span>
                    <?=$singleShop->getAddress();?>
                </label>
            </li>
        <?
        }
        ?>
    </ul>
</dd>