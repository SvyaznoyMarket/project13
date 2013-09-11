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

    <?= $helper->render('product-category/__filter', ['category' => $category, 'productFilter' => $productFilter]) // фильтры ?>

    <?= $helper->render('product/__sorting', []) // сортировка ?>

    <?= $helper->render('product/__list', ['pager' => $productPager]) // листинг ?>

    <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листинг ?>

</div>