<?php
/**
 * @var $page                 \View\User\OrdersPage
 * @var $helper               \Helper\TemplateHelper
 * @var $user                 \Session\User
 * @var $products             \Model\Product\Entity[]
 * @var $favoriteProductsByUi \Model\Favorite\Product\Entity[]
 */
?>

<div class="personalPage">
    <?= $page->render('user/_menu', ['page' => $page]) ?>
	<div class="personalTitle">Избранное</div>

	<div class="table-favorites table table--border-cell-hor">
		<? foreach ($products as $product): ?>
		<div class="table-row">
			<div class="table-favorites__cell-left table-cell">
				<a href="<?= $product->getLink() ?>">
					<img src="<?= $product->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getName()) ?>" class="table-favorites__img">
				</a>
			</div>

			<div class="table-favorites__cell-center table-cell">
				<a href="<?= $product->getLink() ?>" class="table-favorites__name"><?= $product->getName() ?></a>
				<? if ($product->getPrice()): ?>
					<div class="table-favorites__price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>
				<? endif ?>
			</div>

			<div class="table-favorites__cell-right table-cell">
				<div class="btnBuy"><a href="" class="btn-type btn-type--buy js-orderButton jsBuyButton">В корзину</a></div>
				<div class="table-favorites__delete"><a href="" class="undrl">Удалить</a></div>
			</div>
		</div>
		<? endforeach ?>
	</div>
</div>
