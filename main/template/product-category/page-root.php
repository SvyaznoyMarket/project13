<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
$catalogJsonBulk = isset($catalogJsonBulk)?$catalogJsonBulk:[];
?>

<? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfox683"></div>
<? endif ?>

<div class="clear"></div>

<? if(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? else: ?>
	<div class="goodslist clearfix">
	<? foreach ($category->getChild() as $child): ?>
	    <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category, 'catalogJsonBulk' => $catalogJsonBulk)) ?>
	<? endforeach ?>
	</div>
<? endif ?>

<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<? if (\App::config()->crossss['enabled']): ?>
	<div class="lifted mCatalog">
	  <script type="text/html" id="similarGoodTmpl">
	    <div class="bSimilarGoodsSlider_eGoods fl">
	      <a class="bSimilarGoodsSlider_eGoodsImg" href="<%=link%>"><img src="<%=image%>"/></a>
	      <div class="bSimilarGoodsSlider_eGoodsInfo">
	        <div class="goodsbox__rating rate<%=rating%>"><div class="fill"></div></div>
	        <h3><a href="<%=link%>"><%=name%></a></h3>
	        <div class="font18 pb10 mSmallBtns"><span class="price"><%=price%></span> <span class="rubl">p</span></div>
	      </div>
	    </div>
	  </script>
	  <div class="bSimilarGoods clearfix mCatalog">
	  	<h2 class="mt32">
			Популярные товары
		</h2>
	    <div id="similarGoodsSlider" class="bSimilarGoodsSlider" data-url="<?= $page->url('product.category.recommended.slider', ['categoryPath' => $category->getPath()]) ?>">
	      <a class="bSimilarGoodsSlider_eArrow mLeft" href="#"></a>
	      <a class="bSimilarGoodsSlider_eArrow mRight" href="#"></a>
	      <div class="bSimilarGoodsSlider_eWrap clearfix">
	      </div>
	    </div>
	  </div>
	</div>
<? endif ?>
