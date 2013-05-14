<?php
/**
 * @var $page \Terminal\View\ProductLine\IndexPage
 * @var $line \Model\Product\Line\Entity
 */
?>

<article id="categoryData" data-url="<?= $page->url('line.product', ['lineId' => $line->getId()]) ?>" class="bListing bContent mLine mLoading" data-pagetype='product_model_list'>
	<div id="productList"></div>
	<div class="bProductListWrap mModelList clearfix"></div>
</article>