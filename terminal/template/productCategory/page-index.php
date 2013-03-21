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

	<div class="bProductListWrap mSizeBig">
		<div class="bProductListItem">
			<a class="bProductListItem__eImgLink jsRedirect" data-screentype='product' data-productid='123123' href="#"><img class="bProductListItem__eImg" src="" alt="" /></a>
			<div class="bProductListItem__eDesc">
				<p class="bProductListItem__eArticle">#123123</p>
				<h2 class="bProductListItem__eTitle">Название товара</h2>
				<p class="bProductListItem__eFullDesc">Описание товара</p>
				<p class="bProductListItem__ePrice">17000 <span class="bRuble">p</span></p>
				<ul class="bProductListItem__eDlvrBlock">
					<li class="bProductListItem__eDlvrBlockItem">Есть в этом магазине</li>
					<li class="bProductListItem__eDlvrBlockItem">Можно забрать сейчас</li>
				</ul>
				<div class="bProductListItem__eBtnBlock">
					<a class="bProductListItem__eBtn bButton mOrangeBtn mFl jsBuyButton" data-productid='123123' href="#">В корзину</a>
					<a class="bProductListItem__eBtn bButton mGrayBtn mFl jsWhereBuy" data-productid='123123' href="#">Где купить?</a>
					<a id="compare_123123" class="bProductListItem__eBtn jsCompare bButton mGrayBtn mFl" data-productid='123123' href="#">К сравнению</a>
				</div>
			</div>
		</div>
	</div>

</article>