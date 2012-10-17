<?php
/**
 * @var $page           \View\Layout
 * @var $request        \Http\Request
 * @var $pager          \Iterator\EntityPager
 * @var $view           string
 * @var $hasListView    bool                                Показывать контрол "Вид товара"
 * @var $productSorting \Model\Product\Sorting
 * @var $productFilter  \Model\Product\Filter
 * @var $category       \Model\Product\Category\Entity|null
 */

$view = $request->get('view', isset($view) ? $view : null);
$hasListView = isset($hasListView) && (bool)$hasListView ? true : false;
if (!isset($productSorting)) $productSorting = null;
if (!isset($category)) $category = null;
$filterData = isset($productFilter) ? http_build_query(array(\View\Product\FilterForm::$name => $productFilter->getValues())) : '';
?>

<? if ('expanded' == $view) : ?>
<input type="hidden" id="dlvrlinks"
    data-shoplink="<?= $page->url('shop') ?>"
    data-calclink="<?= $page->url('product.delivery') ?>" />
<?php endif ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
    data-url="<?= $page->helper->replacedUrl(array('page' => null)) ?>"
    data-page="<?= $pager->getPage() ?>"
    data-lastpage="<?= $pager->getLastPage() ?>"
    data-filter="<?= $filterData ?>"
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>

<? if ($pager->count() && $hasListView): ?>
<?= $page->render('product/_listView', array('view' => $view, 'request' => $request, 'category' => $category)) ?>
<? endif ?>

<? if ($productSorting): ?>
<?= $page->render('product/_sorting', array('productSorting' => $productSorting)) ?>
<? endif ?>

<? if ($pager->count()): ?>
    <div class="line"></div>
<? endif ?>

<?= $page->render('product/_list', array('pager' => $pager, 'view' => $view)) ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
     data-url="<?= $page->helper->replacedUrl(array('page' => null)) ?>"
     data-page="<?= $pager->getPage() ?>"
     data-lastpage="<?= $pager->getLastPage() ?>"
     data-filter="<?= $filterData ?>"
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>