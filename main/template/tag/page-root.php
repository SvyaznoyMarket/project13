<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 * @var $productVideosByProduct  array
 */

$count = 0;
if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());

$rootCategory = $category;
$categoryTokens = array_keys($sidebarCategoriesTree[$rootCategory->getToken()]);
?>

<? foreach ($childrenById as $id => $child) {
    $pager = $productPagersByCategory[$child->getId()];
    if (!$pager || !$pager->count()) continue;
    $count += $pager->count();
} ?>
<?= $page->render('product/_inshop', ['count' => $count, 'category' => $category]); ?>
<? foreach ($childrenById as $id => $child) { ?>
    <?
    $pager = $productPagersByCategory[$child->getId()];
    if (!$pager || !$pager->count()) continue;
    ?>
    <?= $page->render('tag/_product-slider-inCategory', array(
        'tag'                    => $tag,
        'category'               => $child,
        'pager'                  => $pager,
        'itemsInSlider'          => ceil($pager->getMaxPerPage() / 2),
        'productVideosByProduct' => $productVideosByProduct,
    )) ?>
<? }
$page->setGlobalParam('productCount', $count);
?>
