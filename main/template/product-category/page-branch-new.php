<?php
/**
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$count = 0;
if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());
?>

<div class="bCatalog">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

	<h1  class="bTitlePage"><?= $category->getName() ?></h1>

    <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       =>
            ('product.category.brand' == \App::request()->attributes->get('route') || \App::request()->get('shop'))
            ? $helper->url('product.category', ['categoryPath' => $category->getPath()])
            : ''
        ,
        'productFilter' => $productFilter,
    ]) // фильтры ?>

    <?= $helper->render('product/__listAction', [
        'category'       => $category,
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => $productVideosByProduct,
    ]) // листинг ?>

    <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>

</div>