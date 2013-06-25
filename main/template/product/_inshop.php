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
	<a href="" class="bShopInStock__eAll">показать все</a>
</div>
<div style="float: right">
    <a>показать все.</a>
</div>
<? if (!$count && $renderInfo): ?>
   <div class="bShopInStockNoItem"><strong>Нет товаров</strong></div>
<? endif ?>