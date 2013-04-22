<?php
/**
 * @var $page           \Terminal\View\Product\IndexPage
 * @var $category       \Model\Product\Category\Entity
 * @var $productSorting \Model\Product\TerminalSorting
 */
?>

<article id="categoryData" data-url="<?= $page->url('category.product', ['categoryId' => $category->getId()]) ?>" data-sort="<?= $page->json($productSorting->all()) ?>" class="bListing bContent mLoading" data-pagetype='product_list'>
	<div id="productList"></div>
	<div class="bProductListWrap mSizeLittle clearfix"></div>
</article>