<?php
/**
 * @var $page           \Terminal\View\ProductCategory\IndexPage
 * @var $category       \Model\Product\Category\Entity
 * @var $productSorting \Model\Product\TerminalSorting
 */
?>

<?
$url = $page->url('category.product', ['categoryId' => $category->getId()]);
if ($filterData = \App::request()->get('f')) {
    $url .= (false !== strpos($url, '?') ? '&' : '?') . http_build_query(['f' => $filterData]);
}
if ($sortData = \App::request()->get('sort')) {
    $url .= (false !== strpos($url, '?') ? '&' : '?') . http_build_query(['sort' => $sortData]);
}

?>

<article id="categoryData" data-url="<?= $url ?>" data-sort="<?= $page->json($productSorting->all()) ?>" class="bListing bContent mLoading" data-pagetype='product_list'>
	<div id="productList"></div>
	<div class="bProductListWrap mSizeLittle clearfix"></div>
</article>