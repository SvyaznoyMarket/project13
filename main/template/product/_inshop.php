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
Только товары из магазина <b><?=$shop->getAddress()?></b><br><br>
<? if (!$count && $renderInfo): ?>
    <br>нет товаров
<? endif ?>