<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $count                   integer
 * @var $renderInfo              boolean
 * @var $category                \Model\Product\Category\Entity
 */
/** @var \Model\Shop\Entity $shop */
$shop = $page->getGlobalParam('shop') ? $page->getGlobalParam('shop') : null;
$renderInfo = isset($renderInfo) ? $renderInfo : true;
if (!$shop) return;
?>
<div style="float: left">
    <img style="background: url('/css/newProductCard/img/ico.png') no-repeat 0 1px; padding-left: 25px; margin: 0 0 12px;">Только товары из магазина <b><?=$shop->getAddress()?></b>
</div>
<div style="float: right">
    <a href="<?=$page->url('product.category', ['categoryPath' => $category->getPath()])?>">показать все</a>
</div>
<? if (!$count && $renderInfo): ?>
    <br>нет товаров
<? endif ?>