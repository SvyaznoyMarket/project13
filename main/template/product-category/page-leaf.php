<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 */
if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>
<div class="clear"></div>

<? if(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? endif ?>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>
<?= $page->render('product/_inshop', ['count' => $productPager->count(), 'renderInfo' => false]); ?>
<?= $page->render('product/_pager', array(
    'request'                => $request,
    'pager'                  => $productPager,
    'productFilter'          => $productFilter,
    'productSorting'         => $productSorting,
    'hasListView'            => true,
    'category'               => $category,
    'view'                   => $productView,
    'productVideosByProduct' => $productVideosByProduct,
)) ?>
