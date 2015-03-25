<?php
/**
 * @var $page               \View\DefaultLayout
 * @var $gridCells          \Model\GridCell\Entity[]
 * @var $category           \Model\Product\Category\Entity
 * @var $catalogConfig      array
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity|null
 * @var $productsById       \Model\Product\CompactEntity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];
?>

<?//= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

<? if ((bool)$siblingCategories): ?>
    <?= $helper->render('product-category/__sibling-list', [
        'categories'         => $siblingCategories,
        'catalogConfig'      => $catalogConfig,
        'currentCategory'    => $category,
        'rootCategoryInMenu' => $rootCategoryInMenu
    ]) // категории-соседи ?>
<? endif ?>

<? if (false): ?>
    <h1 class="bTitlePage js-pageTitle"><?= $category->getName() ?></h1>
<? endif ?>


<?
$config = \App::config()->tchibo;

$contentHeight = 0;
foreach ($gridCells as $cell) {
    $height =
        (($cell->getRow() - 1) *  $config['rowWidth'] + ($cell->getRow() - 1) * $config['rowPadding'])
        + ($cell->getSizeY() * $config['rowHeight'] + ($cell->getSizeY() - 1) * $config['rowPadding']);
    if ($height > $contentHeight) {
        $contentHeight = $height;
    }
}
?>
<!-- TCHIBO - листинг Чибо -->
<div class="tchiboProducts" style="position: relative; height: <?= $contentHeight ?>px; margin: 0 0 10px 7px;">
<?= $helper->render('grid/__show', [
    'gridCells'    => $gridCells,
    'productsByUi' => $productsByUi,
]) ?>
</div>
<!--/ TCHIBO - листинг Чибо -->

<div class="clear"></div>

<div style="margin: 0 0 30px;">
    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'count'     => null,
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'categoryPage'
            ],
        ]) ?>
    <? endif ?>
</div>

<div class="clear"></div>