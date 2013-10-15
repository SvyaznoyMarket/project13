<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 * @var $hotlinks               array
 * @var $seoContent             string
 */
?>

<?
    $helper = new \Helper\TemplateHelper();
    if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());
?>

<div class="bCatalog">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

	<h1  class="bTitlePage"><?= $title ?></h1>

    <? if (\App::config()->adFox['enabled']): ?>
    <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
    <? endif ?>

    <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>

    <? if (!empty($promoContent)): ?>
        <?= $promoContent ?>
    <? endif ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
        'countUrl'      => $helper->url('product.category.count', ['categoryPath' => $category->getPath()]),
        'productFilter' => $productFilter,
        'hotlinks'      => $hotlinks,
        'openFilter'    => false,
    ]) // фильтры ?>

    <?= $helper->render('product/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => $productVideosByProduct,
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
</div>