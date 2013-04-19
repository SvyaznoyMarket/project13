<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfox683"></div>
<? endif ?>

<div class="clear"></div>

<div class="goodslist">
<? foreach ($category->getChild() as $child): ?>
    <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category)) ?>
<? endforeach ?>
</div>

<? if (\App::config()->crossss['enabled']): ?>
	<div class="lifted">
	  <script type="text/html" id="similarGoodTmpl">
	    <div class="bSimilarGoodsSlider_eGoods fl">
	      <a class="bSimilarGoodsSlider_eGoodsImg fl" href="<%=link%>"><img width="83" height="83" src="<%=image%>"/></a>
	      <div class="bSimilarGoodsSlider_eGoodsInfo fl">
	        <div class="goodsbox__rating rate<%=rating%>"><div class="fill"></div></div>
	        <h3><a href="<%=link%>"><%=name%></a></h3>
	        <div class="font18 pb10 mSmallBtns"><span class="price"><%=price%></span> <span class="rubl">p</span></div>
	      </div>
	    </div>
	  </script>
	  <div class="bSimilarGoods clearfix">
	    <div class="bSimilarGoods_eCorner"><div></div></div>
	    <div class="bSimilarGoods_eLeftCaption fl">
	      Популярные товары
	    </div>
	    <div id="similarGoodsSlider" class="bSimilarGoodsSlider fr" data-url="<?= $page->url('product.category.recommended.slider', ['categoryPath' => $category->getPath()]) ?>">
	      <a class="bSimilarGoodsSlider_eArrow mLeft" href="#"></a>
	      <a class="bSimilarGoodsSlider_eArrow mRight" href="#"></a>
	      <div class="bSimilarGoodsSlider_eWrap clearfix">
	      </div>
	    </div>
	  </div>
	</div>

<? endif ?>
