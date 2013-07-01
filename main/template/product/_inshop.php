<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $count                   integer
 * @var $renderInfo              boolean
 */
/** @var \Model\Shop\Entity $shop */
$shop = $page->hasGlobalParam('shop') && $page->getGlobalParam('shop') && \App::config()->shop['enabled'] ? $page->getGlobalParam('shop') : null;
$renderInfo = isset($renderInfo) ? $renderInfo : true;
if (!$shop) return;
?>
<div class="bShopInStock clearfix">
	<div class="bShopInStock__eAddress">Только товары из магазина <strong><?=$shop->getAddress()?></strong></div>
	<a href="<?= $page->helper->replacedUrl(array('page' => null, 'shop' => null), null, $request->attributes->get('route')) ?>" class="bShopInStock__eAll">показать все</a>
</div>
<? if (!$count && $renderInfo): ?>
   нет товаров
<? endif ?>