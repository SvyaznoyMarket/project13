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
    <? if (\App::request()->get('shop')): ?>
        <input type="hidden" name="shop" value="<?=\App::request()->get('shop')?>"/>
    <? endif ?>
    <dt>Наличие в магазинах</dt>
    <dd style="display: <?= $shop ? 'block' : 'none' ?>;">
    <ul class="checkbox_list">
        <li data-onclick-location="<?=$page->helper->replacedUrl(array('page' => null, 'shop' => null), null, $request->attributes->get('route'))?>">
            <input name="shop" id="shop_0" type="radio" class="hiddenCheckbox shopFilter"/>
            <label for="shop_0" class="prettyCheckbox radio list <?=$shop?'':'checked';?> ">
                Не важно
            </label>
        </li>
        <? foreach ($shops as $i => $singleShop) { ?>
            <?
                $link = $page->helper->replacedUrl(array('page' => null, 'shop' => null), null, $request->attributes->get('route'));
                $link .= (false === strpos($link, '?') ? '?' : '&') . 'shop='. $singleShop->getId();
            ?>
            <li data-onclick-location="<?=$link?>">
                <input name="shop" id="shop_<?=$i+1?>" type="radio" class="hiddenCheckbox shopFilter"/>
                <label for="shop_<?=$i+1?>" class="prettyCheckbox radio list <?=($shop&&$shop->getId() == $singleShop->getId())?'checked':'';?>">
                    <?=$singleShop->getAddress();?>
                </label>
            </li>
        <? } ?>
    </ul>
    </dd>
<? endif ?>