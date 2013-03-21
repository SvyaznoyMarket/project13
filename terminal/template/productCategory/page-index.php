<?php
/**
 * @var $page    \Terminal\View\Product\IndexPage
 * @var $data[]
 * @var $products \Model\Product\TerminalEntity[]
 */
$productData = [];
?>

<article id="categoryData" data-data="<?= $page->json($data) ?>" class="bGoodItem bContent" data-pagetype='product_list'>
	<? foreach ($products as $product): ?>
	    <? $productData[] = [
	        'id' => $product->getId(),
	        'name' => $product->getName(),
	        'image' => $product->getImageUrl(3),
	        'article' => $product->getArticle(),
	        'price' => $product->getPrice(),
	        'isBuyable' => $product->getIsBuyable(\App::config()->region['shop_id']),
	    ] ?>
	<? endforeach ?>

	<div id="productList" data-product="<?= $page->json($productData) ?>"></div>


</article>