<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $count                   integer
 * @var $renderInfo              boolean
 */
/** @var \Model\Shop\Entity $shop */
$shop = $page->getGlobalParam('shop') ? $page->getGlobalParam('shop') : null;
$renderInfo = isset($renderInfo) ? $renderInfo : true;
if (!$shop) return;
?>
<div class="bShopInStock clearfix">
	<div class="bShopInStock__eAddress">Только товары из магазина <strong><?=$shop->getAddress()?></strong></div>
	<a href="<?=$page->url('product.category', ['categoryPath' => $category->getPath()])?>" class="bShopInStock__eAll">показать все</a>
</div>
<? if (!$count && $renderInfo): ?>
   нет товаров
<? endif ?>