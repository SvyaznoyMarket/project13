<?php

$f = function (
    \Model\Product\BasicEntity $product
) {
?>
	    <div class="productInner__imgBox">
	    	<a class="productInner__imgLink" href="">
	    		<img class="productInner__img" src="http://fs05.enter.ru/1/1/500/32/251956.jpg" alt="" />
	    	</a>
	    </div><!--/ картинка продукта -->

	    <div class="productInner__desc">
	    	<div class="inner">
			    <a class="name" href="<?= $product->getLink() ?>"><?= $product->getName() ?></a>

			    <div class="clearfix">
				    <span class="price" class="">10 344 <span class="rubl">p</span></span> 

				    <span class="bOptions"><span class="bDecor">Варианты</span></span>
				</div>

			    <div class="bOptionsSection">
	                <i class="bCorner"></i>
	                <i class="bCornerDark"></i>

	                <ul class="bOptionsList">
	                    <li class="bOptionsList__eItem">Объем</li>
	                </ul>
	            </div>
			    
			    <div class="bBtnLine clearfix">
	                <div class="btnBuy">
					    <a href="/cart/add-product/126815" class="id-cartButton-product-126815 jsBuyButton btnBuy__eLink" data-group="126815" data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/126815&quot;,&quot;fromUpsale&quot;:false}">
					        Купить
					    </a>
					</div>

				    <a class="btnView mBtnGrey" href="/product/household/zavarochniy-chaynik-s-sitechkom-bodum-chambord-1-l-2040101033246">Посмотреть</a>
				</div>
			</div>
		</div><!--/ описание продукта -->
<?
}; return $f;