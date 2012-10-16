<?php
/**
 * @var $page          \View\DefaultLayout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $productPager  \Iterator\EntityPager
 * @var $productView   string
 */
?>

<? require __DIR__ . '/_banner.php' ?>

<div class="clear"></div>

<?= $page->render('product/_pager', array(
    'request'       => $request,
    'pager'         => $productPager,
    'productFilter' => $productFilter,
    'hasListView'   => true,
    'category'      => $category,
    'view'          => $productView,
)) ?>