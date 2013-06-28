<?php
/**
 * @var $page                   \View\Layout
 * @var $request                \Http\Request
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $hasListView            bool                                Показывать контрол "Вид товара"
 * @var $productSorting         \Model\Product\Sorting
 * @var $productFilter          \Model\Product\Filter
 * @var $category               \Model\Product\Category\Entity|null
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool                                Показывать ли дополнительную информацию
 */

$view = $request->get('view', isset($view) ? $view : null);
$hasListView = isset($hasListView) && (bool)$hasListView ? true : false;
if (!isset($productSorting)) $productSorting = null;
if (!isset($category)) $category = null;
$filterData = isset($productFilter) ? http_build_query(array(\View\Product\FilterForm::$name => $productFilter->getValues())) : '';
if (!isset($productVideosByProduct)) $productVideosByProduct = [];
if (!isset($isAddInfo)) $isAddInfo = false;
?>

<? if ('expanded' == $view) : ?>
<input type="hidden" id="dlvrlinks"
    data-shoplink="<?= $page->url('shop') ?>"
    data-calclink="<?= $page->url('old.product.delivery') ?>" />
<?php endif ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
    data-url="<?= $page->helper->replacedUrl(array('page' => null), null, $request->attributes->get('route') . (strpos($request->attributes->get('route'), 'infinity') !== false ? '' : '.infinity')) ?>"
    data-page="<?= $pager->getPage() ?>"
    data-lastpage="<?= $pager->getLastPage() ?>"
    data-filter="<?= $filterData ?>"
></div>
<? if(!empty($showPagerHeader)): ?>
<span class="mBoldh3"><?= $pager->count() . ' ' . $page->helper->numberChoice($pager->count(), array('товар', 'товара', 'товаров')) ?></span>
<? endif ?>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>
<?
if ($pager->count()) $page->setGlobalParam('productCount', $pager->count());
?>
<? if ($pager->count() && $hasListView): ?>
<?= $page->render('product/_listView', array('view' => $view, 'request' => $request, 'category' => $category)) ?>
<? endif ?>

<? if ($productSorting && $pager->count()): ?>
<?= $page->render('product/_sorting', array('productSorting' => $productSorting)) ?>
<? endif ?>

<? if ($pager->count()): ?>
    <div class="line"></div>
<? endif ?>

<?= $page->render('product/_list', array('pager' => $pager, 'view' => $view, 'productVideosByProduct' => $productVideosByProduct, 'isAddInfo' => $isAddInfo)) ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
     data-url="<?= $page->helper->replacedUrl(array('page' => null), null, $request->attributes->get('route') . (strpos($request->attributes->get('route'), 'infinity') !== false ? '' : '.infinity')) ?>"
     data-page="<?= $pager->getPage() ?>"
     data-lastpage="<?= $pager->getLastPage() ?>"
     data-filter="<?= $filterData ?>"
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>