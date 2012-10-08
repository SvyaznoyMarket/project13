<?php
/**
 * @var $page        \View\DefaultLayout
 * @var $request     \Http\Request
 * @var $pager       \Iterator\EntityPager
 * @var $view        string
 * @var $hasListView bool                                Показывать контрол "Вид товара"
 * @var $hasSorting  bool                                Показывать контрол "Сортировка"
 * @var $category    \Model\Product\Category\Entity|null
 */

$view = $request->get('view', isset($view) ? $view : null);
$hasListView = isset($hasListView) && (bool)$hasListView ? true : false;
$hasSorting = isset($hasSorting) && (bool)$hasSorting ? true : false;
if (!isset($category)) $category = null;
?>

<? if ('expanded' == $view) : ?>
<input type="hidden" id="dlvrlinks"
    data-shoplink="<?= $page->url('shop') ?>"
    data-calclink="<?= $page->url('product.delivery') ?>" />
<?php endif ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
    data-url="<?= $request->getRequestUri() ?>"
    data-page="<?= $pager->getPage() ?>"
    data-mode="<?= $view ?>"
    data-lastpage="<?= $pager->getLastPage() ?>"
    data-filter=""
></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>

<? if ($pager->count() && $hasListView): ?>
<?= $page->render('product/_listView', array('view' => $view, 'request' => $request, 'category' => $category)) ?>
<? endif ?>

<? if ($hasSorting): ?>
<?php //include_component('product', 'sorting', array('productSorting' => $productSorting)) ?>
<? endif ?>

<? if ($pager->count()): ?>
    <div class="line"></div>
<? endif ?>

<?//= $page->render('product/_list', array('pager' => $pager, 'view' => $view)) ?>

<? if ($pager->hasPages()): ?>
<div class="fr allpager mBtn" alt="все товары в категории" title="все товары в категории"
     data-url="<?= $request->getRequestUri() ?>"
     data-page="<?= $pager->getPage() ?>"
     data-mode="<?= $view ?>"
     data-lastpage="<?= $pager->getLastPage() ?>"
     data-filter=""
        ></div>
<?= $page->render('_pagination', array('pager' => $pager)) ?>
<? endif ?>