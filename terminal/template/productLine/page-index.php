<?php
/**
 * @var $page \Terminal\View\ProductLine\IndexPage
 * @var $line \Model\Product\Line\Entity
 */
?>

<article id="lineData" data-url="<?= $page->url('line.product', ['lineId' => $line->getId()]) ?>" class="bListing bContent mLoading" data-pagetype='product_list'>
	<div id="productList"></div>
	<div class="bProductListWrap mSizeLittle clearfix"></div>
</article>