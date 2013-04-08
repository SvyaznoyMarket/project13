<?php
/**
 * @var $page     \Terminal\View\Product\IndexPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<article id="categoryData" data-url="<?= $page->url('category.product', ['categoryId' => $category->getId()]) ?>" class="bListing bContent" data-pagetype='product_list'>
	<div id="productList"></div>
	<div class="bProductListWrap mSizeLittle clearfix"></div>
</article>