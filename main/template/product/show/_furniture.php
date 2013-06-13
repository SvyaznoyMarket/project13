<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<div class="bProductCard__eArticle clearfix">
	<span>Артикул: <span itemprop="productID"><?= $product->getArticle() ?></span></span>
</div>

<div id="planner3D" class="bPlanner3D fl" data-cart-sum-url="<?= $page->url('cart.sum') ?>"></div>

<div class="bProductCardRightCol fr">
	<div class="bProductCardRightCol__eInner">
		<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<? if ($product->getIsBuyable()): ?>
				<link itemprop="availability" href="http://schema.org/InStock" />
				<div class="pb5"><strong class="orange">Есть в наличии</strong></div>
			<? elseif (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
				<link itemprop="availability" href="http://schema.org/InStoreOnly" />
			<? else: ?>
				<link itemprop="availability" href="http://schema.org/OutOfStock" />
			<? endif ?>
			
			<? if($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
	            <div style="text-decoration: line-through; font: normal 18px verdana; letter-spacing: -0.05em; color: #6a6a6a;"><span class="price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
	        <? elseif($showAveragePrice): ?>
	            <div class="mOurGray">
	                Средняя цена в магазинах города*<br/><div class='mOurGray mIco'><span class="price"><?= $page->helper->formatPrice($product->getPriceAverage()) ?></span> <span class="rubl">p</span> &nbsp;</div>
	            </div>
	            <div class="clear"></div>
	            <div class="clear mOur pt10 <? if ($product->hasSaleLabel()) echo 'red'; ?>">Наша цена</div>
	        <? endif ?>
			<div class="mb10 <? if ($product->hasSaleLabel()) echo 'red'; ?>">
				<span class="bProductCardRightCol__ePrice" itemprop="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <meta itemprop="priceCurrency" content="RUB"><span class="bProductCardRightCol__eCurrency rubl">p</span>
			</div>
		</div>

		<? if ($product->getIsBuyable()): ?>
			<? if ($dataForCredit['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany()) : ?>
				<div class="bProductCardRightCol__eCreditBox creditbox">
					<div class="creditboxinner">
						<label class="bigcheck" for="creditinput"><b></b>
							<span class="bProductCardRightCol__eCreditBox-Take">Беру в кредит</span>
							<input id="creditinput" type="checkbox" name="creditinput" autocomplete="off"/>
						</label>
						<div class="bProductCardRightCol__eCreditBox-Sum">от <span class=""><b class="price"></b> <b class="rubl">p</b></span> в месяц</div>
					</div>
				</div>
			<? endif; ?>

			<? if ($dataForCredit['creditIsAllowed']) : ?>
				<input data-model="<?= $page->escape($dataForCredit['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
			<? endif; ?>

		<? elseif ($user->getRegion()->getHasTransportCompany()): ?>
			<? if (\App::config()->product['globalListEnabled'] && (bool)$product->getNearestCity()): ?>
				<?= $page->render('product/_nearestCity', ['product' => $product]) ?>
			<? else: ?>
				<p>Этот товар мы доставляем только в регионах нашего присутствия</p>
			<? endif ?>
		<?php endif ?>

		<div class="goodsbarbig mSmallBtns" ref="<?= $product->getToken() ?>" data-value='<?= $json ?>'>
			<? if ($product->getIsBuyable()): ?>
				<div class='bCountSet'>
					<a class="bCountSet__eM <? if ($user->getCart()->hasProduct($product->getId())) echo 'disabled'; ?>" href="#">-</a>
					<span><?= $user->getCart()->hasProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?></span>
					<a class="bCountSet__eP <? if ($user->getCart()->hasProduct($product->getId())) echo 'disabled'; ?>" href="#">+</a> шт.
				</div>
			<?php endif ?>
			<?= $page->render('cart/_button', ['product' => $product, 'disabled' => !$product->getIsBuyable()]) ?>
			<a href=""
				data-model='<?= $json ?>'
				link-output='<?= $page->url('order.1click', ['product' => $product->getToken()]) ?>'
				link-input='<?= $page->url('product.delivery_1click') ?>'
				class="font14 underline order1click-link-new">Купить быстро в 1 клик</a>
		</div>
	</div>
	<? if ($product->getIsBuyable()): ?>
		<?= $page->render('service/_listByProduct', ['product' => $product]) ?>
		<?= $page->render('warranty/_listByProduct', ['product' => $product]) ?>
	<? endif ?>
</div>