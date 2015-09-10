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
 * @var $isAddInfo              bool                                Показывать ли дополнительную информацию
 */

$view = isset($view) ? $view : null;
$hasListView = isset($hasListView) && (bool)$hasListView ? true : false;
if (!isset($productSorting)) $productSorting = null;
if (!isset($category)) $category = null;
$filterData = isset($productFilter) ? http_build_query(array(\View\Product\FilterForm::$name => $productFilter->getValues())) : '';
if (!isset($isAddInfo)) $isAddInfo = false;
?>

<? if ($pager->hasPages()): ?>
<div class="fr allpagerJewel mBtn" alt="все товары в категории" title="все товары в категории"
     data-url="<?= $page->helper->replacedUrl(array('page' => null), null, $request->attributes->get('route') . ( strpos($request->attributes->get('route'), 'sliderInfinity') !== false ? '' : '.sliderInfinity') ) ?>"
     data-page="<?= $pager->getPage() ?>"
     data-lastpage="<?= $pager->getLastPage() ?>"
     data-filter="<?= $filterData ?>"
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>

<? if ($pager->count()): ?>
    <div class="line"></div>
<? endif ?>

<?= $page->render('jewel/product/_list', [
    'pager' => $pager,
    'view' => $view,
    'isAddInfo' => $isAddInfo,
    'category' => $category,
    'itemsPerRow' => $page->getParam('itemsPerRow')
]) ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpagerJewel mBtn" alt="все товары в категории" title="все товары в категории"
     data-url="<?= $page->helper->replacedUrl(array('page' => null), null, $request->attributes->get('route') . ( strpos($request->attributes->get('route'), 'sliderInfinity') !== false ? '' : '.sliderInfinity') ) ?>"
     data-page="<?= $pager->getPage() ?>"
     data-lastpage="<?= $pager->getLastPage() ?>"
     data-filter="<?= $filterData ?>"
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>