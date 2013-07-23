<?php
/**
 * @var $page           \View\Product\IndexPage
 * @var $user           \Session\User
 * @var $line           \Model\Line\Entity
 * @var $mainProduct    \Model\Product\Entity
 * @var $parts          \Model\Product\Entity[]
 * @var $request        \Http\Request
 * @var $productPager   \Iterator\EntityPager|NULL
 * @var $productView    string
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="bMainContainer bProductSection mProductSectionSet clearfix">
	<div class="bProductSection__eLeft" data-value="<?= $page->json([
        'jsref' =>   $mainProduct->getToken(),
        'jsimg' =>   $mainProduct->getImageUrl(3),
        'jstitle' => $page->escape($mainProduct->getName()),
        'jsprice' => $mainProduct->getPrice(),
    ]) ?>">
		<div class="bProductDesc__ePhoto">
	        <div class="bProductDesc__ePhoto-bigImg">
		        <a href="<?= $mainProduct->getLink() ?>" title="<?= $mainProduct->getName() ?>">
		            <? if ((bool)$mainProduct->getLabel()): ?>
		                <img class="bLabels" src="<?= $mainProduct->getLabel()->getImageUrl(1) ?>" alt="<?= $mainProduct->getLabel()->getName() ?>" />
		            <? endif ?>
		            <img src="<?= $mainProduct->getImageUrl(3) ?>" alt="<?= $page->escape($mainProduct->getName()) ?>" width="700" height="700" title="<?= $page->escape($mainProduct->getName()) ?>"/>
		        </a>
	        </div>
        </div>
	</div>

	<div class="bProductSection__eRight">
		<p class="bProductDescText"><?= $mainProduct->getTagline() ?></p>

        <div class="bProductDescMore">
            <div class='bProductDescMore__eTWrap'>
                <a class='bProductDescMore__eMoreInfo' href="<?= $mainProduct->getLink() ?>">
                    Подробнее о <?= count($mainProduct->getKit())  ? 'наборе' : 'товаре' ?>
                </a>
            </div>
        </div>

		<div class="bWidgetBuy mWidget">
			<div class="bStoreDesc">
                <?= $helper->render('product/__state', ['product' => $mainProduct]) // Есть в наличии ?>

                <?= $helper->render('product/__price', ['product' => $mainProduct]) // Цена ?>

                <?= $helper->render('product/__notification-lowerPrice', ['product' => $mainProduct]) // Узнать о снижении цены ?>

                <?//= $helper->render('product/__credit', ['product' => $mainProduct, 'creditData' => $creditData]) // Беру в кредит ?>
        	</div>

            <?= $helper->render('cart/__button-product', ['product' => $mainProduct, 'class' => 'btnBuy__eLink', 'value' => 'Купить']) // Кнопка купить ?>

            <?= $helper->render('product/__oneClick', ['product' => $mainProduct]) // Покупка в один клик ?>

            <?= $helper->render('product/__delivery', ['product' => $mainProduct]) // Доставка ?>

            <div class="bAwardSection"><img src="/css/newProductCard/img/award.jpg" alt="" /></div>
        </div><!--/widget delivery -->
	</div>

	<div class="clear"></div>

	<? if (count($mainProduct->getKit())): ?>
        <?= $helper->render('product/__slider', [
            'title'    => 'Состав набора',
            'products' => $parts,
            'class'    => 'mSliderAction840',
        ]) ?>
    <? endif ?>

	<? if ((bool)$productPager): ?>
		<div class="bProductList">
			<h3 class="bHeadSection">Товары серии <?= $line->getName() ?></h3>
			<?= $page->render('product/_list', ['pager' => $productPager, 'view' => $productView, 'itemsPerRow' => 4]) ?>
		</div>
	<? endif ?>

    <? if ($mainProduct->getIsBuyable()): ?>
        <?= $page->render('order/form-oneClick') ?>
    <? endif ?>

</div>