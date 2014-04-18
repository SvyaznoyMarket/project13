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

    <?= $helper->render('product/__data', ['product' => $mainProduct]) ?>

	<div class="bProductSectionLeftCol" data-value="<?= $page->json([
        'jsref' =>   $mainProduct->getToken(),
        'jsimg' =>   $mainProduct->getImageUrl(3),
        'jstitle' => $page->escape($mainProduct->getName()),
        'jsprice' => $mainProduct->getPrice(),
    ]) ?>">
		<div class="bProductDescImg">
	        <div class="bProductDescImgBig">
		        <a href="<?= $mainProduct->getLink() ?>" title="<?= $mainProduct->getName() ?>">
		            <? if ((bool)$mainProduct->getLabel()): ?>
		                <img class="bProductDescSticker" src="<?= $mainProduct->getLabel()->getImageUrl(0) ?>" alt="<?= $mainProduct->getLabel()->getName() ?>" />
		            <? endif ?>
		            <img class="bProductDescImgBig__eImg" src="<?= $mainProduct->getImageUrl(3) ?>" alt="<?= $page->escape($mainProduct->getName()) ?>" width="700" height="700" title="<?= $page->escape($mainProduct->getName()) ?>"/>
		        </a>
	        </div>
        </div>

        <!-- Состав комплекта -->
        <div class="packageSet">
            <div class="packageSetHead cleared">
                <span class="packageSetHead_title">Базовая комплектация набора</span>
                <span class="packageSetHead_change"><span class="packageSetHead_changeText jsChangePackageSet">Изменить комплектацию</span></span>
            </div>

            <!-- элемент комплекта -->
            <div class="packageSetBodyItem">
                <a class="packageSetBodyItem_img" href=""><img src="http://fs07.enter.ru/1/1/120/7e/251766.jpg" /></a><!--/ изображение товара -->

                <div class="packageSetBodyItem_desc">
                    <div class="name"><a class="" href="">Кровать ипрочие слова названия </a></div><!--/ название товара -->

                    <!-- размеры товара -->
                    <div class="column dimantion">
                        <span class="dimantion_name">Высота</span>
                        <span class="dimantion_val">123</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Ширина</span>
                        <span class="dimantion_val">4</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Глубина</span>
                        <span class="dimantion_val">34</span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val">см</span>
                    </div>
                    <!--/ размеры товара -->
                </div>

                <div class="packageSetBodyItem_delivery">
                    Доставка <strong>25.02.12</strong>
                </div><!--/ доставка -->

                <div class="packageSetBodyItem_price">
                    1 232 <span class="rubl">p</span>
                </div><!--/ цена -->

                <div class="packageSetBodyItem_qnt">2 шт.</div><!--/ количество в наборе -->
            </div><!--/ элемент комплекта -->

            <!-- элемент комплекта -->
            <div class="packageSetBodyItem">
                <a class="packageSetBodyItem_img" href=""><img src="http://fs07.enter.ru/1/1/120/7e/251766.jpg" /></a><!--/ изображение товара -->

                <div class="packageSetBodyItem_desc">
                    <div class="name"><a class="" href="">Кровать ипрочие слова названия </a></div><!--/ название товара -->

                    <!-- размеры товара -->
                    <div class="column dimantion">
                        <span class="dimantion_name">Высота</span>
                        <span class="dimantion_val">123</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Ширина</span>
                        <span class="dimantion_val">4</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Глубина</span>
                        <span class="dimantion_val">34</span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val">см</span>
                    </div>
                    <!--/ размеры товара -->
                </div>

                <div class="packageSetBodyItem_delivery">
                    Доставка <strong>25.02.12</strong>
                </div><!--/ доставка -->

                <div class="packageSetBodyItem_price">
                    1 232 <span class="rubl">p</span>
                </div><!--/ цена -->

                <div class="packageSetBodyItem_qnt">2 шт.</div><!--/ количество в наборе -->
            </div><!--/ элемент комплекта -->

            <!-- элемент комплекта -->
            <div class="packageSetBodyItem">
                <a class="packageSetBodyItem_img" href=""><img src="http://fs07.enter.ru/1/1/120/7e/251766.jpg" /></a><!--/ изображение товара -->

                <div class="packageSetBodyItem_desc">
                    <div class="name"><a class="" href="">Кровать ипрочие слова названия </a></div><!--/ название товара -->

                    <!-- размеры товара -->
                    <div class="column dimantion">
                        <span class="dimantion_name">Высота</span>
                        <span class="dimantion_val">123</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Ширина</span>
                        <span class="dimantion_val">4</span>
                    </div>

                    <div class="column separation">x</div>

                    <div class="column dimantion">
                        <span class="dimantion_name">Глубина</span>
                        <span class="dimantion_val">34</span>
                    </div>

                    <div class="column dimantion">
                        <span class="dimantion_name">&nbsp;</span>
                        <span class="dimantion_val">см</span>
                    </div>
                    <!--/ размеры товара -->
                </div>

                <div class="packageSetBodyItem_delivery">
                    Доставка <strong>25.02.12</strong>
                </div><!--/ доставка -->

                <div class="packageSetBodyItem_price">
                    1 232 <span class="rubl">p</span>
                </div><!--/ цена -->

                <div class="packageSetBodyItem_qnt">2 шт.</div><!--/ количество в наборе -->
            </div><!--/ элемент комплекта -->
        </div>
        <!--/ Состав комплекта -->
	</div>

	<div class="bProductSectionRightCol">
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

                <?//= $helper->render('product/__credit', ['product' => $mainProduct, 'creditData' => $creditData]) // Купи в кредит ?>
        	</div>

            <?= $helper->render('cart/__button-product', ['product' => $mainProduct, 'class' => 'btnBuy__eLink', 'value' => 'Купить']) // Кнопка купить ?>

            <?= $helper->render('cart/__button-product-oneClick', ['product' => $mainProduct]) // Покупка в один клик ?>

            <?= $helper->render('product/__delivery', ['product' => $mainProduct]) // Доставка ?>

            <div class="bAwardSection"><img src="/css/bProductSection/img/award.jpg" alt="" /></div>
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
        <?= $page->render('order/form-oneClick', ['product' => $mainProduct]) ?>
    <? endif ?>
</div>