<?php
/**
 * @var $page           \View\Product\IndexPage
 * @var $line           \Model\Line\Entity
 * @var $mainProduct    \Model\Product\Entity
 * @var $parts          \Model\Product\Entity[]
 * @var $request        \Http\Request
 * @var $productPager   \Iterator\EntityPager|NULL
 * @var $productView    string
 */

#JSON data
$json = array(
'jsref' =>      $mainProduct->getToken(),
'jsimg' =>      $mainProduct->getImageUrl(3),
'jstitle' =>    $page->escape($mainProduct->getName()),
'jsprice' =>    $mainProduct->getPrice(),// formatPrice($product->getPrice())
)
?>

<div class="bMainContainer bProductSection mProductSectionSet clearfix">
	<div class="bMainContainer__eHeader">
	    <!-- <div class="bMainContainer__eHeader-subtitle"><?//= $product->getType()->getName() ?></div>-->
	    <h1 class="bMainContainer__eHeader-title"><?php echo $mainProduct->getName() ?></h1>
	    <span class="bMainContainer__eHeader-article">Артикул: <?php echo $mainProduct->getArticle() ?></span>
	</div><!--/head section -->


	<div class="bProductSection__eLeft" data-value='<?php echo json_encode($json) ?>'>
		<div class="bProductDesc__ePhoto">
	        <div class="bProductDesc__ePhoto-bigImg">
		        <a href="<?php echo $mainProduct->getLink() ?>" title="<?php echo $mainProduct->getName() ?>">
		            <?php if ((bool)$mainProduct->getLabel()): ?>
		            <img class="bLabels" src="<?php echo $mainProduct->getLabel()->getImageUrl(1) ?>" alt="<?php echo $mainProduct->getLabel()->getName() ?>" />
		            <?php endif ?>
		            <img src="<?php echo $mainProduct->getImageUrl(3) ?>" alt="<?php echo $mainProduct->getName() ?>" width="700" height="700" title=""/>
		        </a>
	        </div>
        </div>
	</div>

	<div class="bProductSection__eRight">
		<aside>
			<? /*<p class=''><?php echo $mainProduct->getDescription() ?></p> */?>
			<p class="bProductDescText">Классический спальный гарнитур в цвете «венге светлый».</p>

	        <div class="bProductDescMore">
	            <div class='bProductDescMore__eTWrap'>
	                <a class='bProductDescMore__eMoreInfo' href="<?php echo $mainProduct->getLink() ?>">
	                    Подробнее о <?php echo count($mainProduct->getKit())  ? 'наборе' : 'товаре' ?>
	                </a>
	            </div>
	        </div>

			<div class="bWidgetBuy mWidget">
				<div class="bStoreDesc">
            		<div class="inStock">Есть в наличии</div>

		        	<div class="priceOld"><span>1 456</span> <span class="rubl">p</span></div>
		        	<div class="bPrice"><strong><?php echo $page->helper->formatPrice($mainProduct->getPrice()) ?></strong> <span class="rubl">p</span></div>

		            <div class="priceSale">
		                <span class="dotted jsLowPriceNotifer">Узнать о снижении цены</span>
		            </div>

		            <div class="creditbox" style="display: block;">
		                <label class="bigcheck" for="creditinput"><b></b>
		                    <span class="dotted">Беру в кредит</span>
		                    <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off">
		                </label>

		                <div class="creditbox__sum">от <strong>287</strong> <span class="rubl">p</span> в месяц</div>
		                <input data-model="{&quot;price&quot;:5250,&quot;name&quot;:&quot;\u0421\u043c\u0430\u0440\u0442\u0444\u043e\u043d HTC WildFire S \u0441\u0435\u0440\u0435\u0431\u0440\u0438\u0441\u0442\u044b\u0439&quot;,&quot;count&quot;:0,&quot;product_type&quot;:&quot;electronics&quot;,&quot;session_id&quot;:&quot;3pd61hbdhdthidgfi0cqroog63&quot;}" id="dc_buy_on_credit_451-4744" name="dc_buy_on_credit" type="hidden">
		            </div>
	        	</div>

	            <div class="bWidgetBuy__eBuy btnBuy">      
					<a href="" class="btnBuy__eLink">В корзину</a>
        		</div><!--/button buy -->

	            <div class="bWidgetBuy__eClick">
	                <a href="#" class="jsOrder1click">Купить быстро в 1 клик</a>
	            </div>

	            <ul class="bWidgetBuy__eDelivery" data-value="{&quot;url&quot;:&quot;\/ajax\/product\/delivery&quot;}">
				    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-price">              
				    	<span>Доставка <strong>290</strong> <span class="rubl">p</span></span>              
				    	<div>завтра (10.07.2013)</div> 
				    </li>

				    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-text">Оператор контакт-cENTER согласует точную дату за 2-3 дня</li>
				</ul>

	            <div class="bAwardSection"><img src="/css/newProductCard/img/award.jpg" alt="" /></div>
	        </div><!--/widget delivery -->
		</aside>
	</div>

	<?php if (count($mainProduct->getKit())): ?>
		<h3 class="bHeadSection">Состав набора</h3>

		<div class="bSliderAction mSliderAction840">
		    <div class="bSliderAction__eInner">
		        <ul class="bSliderAction__eList clearfix">
		        	<?php foreach ($parts as $part): ?>
		            <li class="bSliderAction__eItem">
		                <div class="product__inner">
		                    <a class="productImg" href="" href="<?php echo $part->getLink() ?>" title="<?php echo $part->getName() ?>">
		                    	<img src="<?php echo $part->getImageUrl(1) ?>" alt="<?php echo $part->getName() ?>" />
		                    </a>
		                    <div class="productName"><a href="">Медиаплеер Apple TV MD199RU/A</a></div>
		                    <div class="productPrice"><span class="price">4 300 <span class="rubl">p</span></span></div>
		                    <div class="btnBuy">  
								<a href="" class="btnBuy__eLink">В корзину</a>
		                    </div>
		                </div>
		            </li>
		            <?php endforeach ?>
		        </ul>
		    </div>

		    <div class="bSliderAction__eBtn mPrev mDisabled"><span></span></div>
            <div class="bSliderAction__eBtn mNext mDisabled"><span></span></div>
		</div>
    <?php endif ?>

	<?php if ((bool)$productPager): ?>
		<div class="bProductList">
			<h3 class="bHeadSection">Товары серии <?php echo $line->getName() ?></h3>
			<?php echo $page->render('product/_list', array('pager' => $productPager, 'view' => $productView, 'itemsPerRow' => 4)) ?>
		</div>
	<?php endif ?>

</div>