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
?>

<?
    $helper = new \Helper\TemplateHelper();
    if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());
?>

<div class="bCatalog">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

	<h1  class="bTitlePage"><?= $category->getName() ?></h1>

    <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
        'countUrl'      => $helper->url('product.category.count', ['categoryPath' => $category->getPath()]),
        'productFilter' => $productFilter,
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

    <div class="bSeoText">
        <p class="bSeoText__eText">Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться. Lorem Ipsum используют потому, что тот обеспечивает более или менее стандартное заполнение шаблона, а также реальное распределение букв и пробелов в абзацах, которое не получается при простой дубликации "Здесь ваш текст.. Здесь ваш текст.. </p>

        <ul class="bSeoText__eList">
            <li class="bSeoText__eListItem">Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться</li>

            <li class="bSeoText__eListItem">Здесь ваш текст.." Многие программы электронной вёрстки и редакторы HTML </li>

            <li class="bSeoText__eListItem">За прошедшие годы текст Lorem Ipsum получил много версий.</li>
        </ul>

        <p class="bSeoText__eText">Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться. Lorem Ipsum используют потому, что тот обеспечивает более или менее стандартное заполнение шаблона, а также реальное распределение букв и пробелов в абзацах, которое не получается при простой дубликации "Здесь ваш текст.. Здесь ваш текст.. </p>
    </div>
</div>